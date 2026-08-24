#!/usr/bin/env python3

from __future__ import annotations

import json
import os
import posixpath
import stat
import sys
import tempfile
import zipfile
from pathlib import Path, PurePosixPath
from typing import Any


REPO_ROOT = Path(__file__).resolve().parents[1]
CONFIG_PATH = Path(
    os.environ.get("DEPLOY_SFTP_CONFIG", REPO_ROOT / ".vscode" / "sftp.json")
).resolve()
ZIP_PATH = Path(
    os.environ.get("DEPLOY_ZIP_PATH", REPO_ROOT / "baltimore.zip")
).resolve()


def fail(message: str) -> None:
    print(f"Deployment aborted: {message}", file=sys.stderr)
    raise SystemExit(1)


def load_configuration() -> dict[str, Any]:
    if not CONFIG_PATH.is_file():
        fail(
            f"SFTP config not found: {CONFIG_PATH}. "
            "Create .vscode/sftp.json or set DEPLOY_SFTP_CONFIG."
        )

    try:
        config = json.loads(CONFIG_PATH.read_text(encoding="utf-8"))
    except (OSError, json.JSONDecodeError) as exc:
        fail(f"could not read SFTP config: {exc}")

    if config.get("protocol") != "sftp":
        fail(".vscode/sftp.json must use protocol 'sftp'")

    required = ("host", "username", "password", "remotePath")
    missing = [name for name in required if not config.get(name)]
    if missing:
        fail(f"missing required SFTP setting(s): {', '.join(missing)}")

    try:
        config["port"] = int(config.get("port", 22))
    except (TypeError, ValueError):
        fail("SFTP port must be a number")

    remote_root_raw = str(config["remotePath"]).rstrip("/")
    remote_root = posixpath.normpath(remote_root_raw)

    if not remote_root_raw.startswith("/"):
        fail("remotePath must be absolute")

    if remote_root_raw != remote_root:
        fail("remotePath must be normalized and may not contain . or .. segments")

    remote_parts = PurePosixPath(remote_root).parts
    if tuple(remote_parts[-3:]) != ("wp-content", "themes", "baltimore"):
        fail("remotePath must end exactly in /wp-content/themes/baltimore")

    config["remotePath"] = remote_root
    return config


def extract_bundle() -> tuple[tempfile.TemporaryDirectory[str], Path]:
    if not ZIP_PATH.is_file():
        fail(f"production bundle not found: {ZIP_PATH}. Run npm run bundle first.")

    temp_dir = tempfile.TemporaryDirectory(prefix="baltimore-deploy-")
    temp_root = Path(temp_dir.name).resolve()

    try:
        with zipfile.ZipFile(ZIP_PATH) as archive:
            for entry in archive.infolist():
                entry_path = PurePosixPath(entry.filename)
                if entry_path.is_absolute() or ".." in entry_path.parts:
                    fail(f"unsafe path in production bundle: {entry.filename}")

            archive.extractall(temp_root)
    except (OSError, zipfile.BadZipFile) as exc:
        temp_dir.cleanup()
        fail(f"could not extract production bundle: {exc}")

    local_root = temp_root / "baltimore"
    if not local_root.is_dir():
        temp_dir.cleanup()
        fail("production bundle does not contain the expected baltimore theme directory")

    return temp_dir, local_root


def remote_lstat(sftp: Any, path: str):
    try:
        return sftp.lstat(path)
    except OSError:
        return None


def ensure_remote_directory(sftp: Any, path: str) -> None:
    attributes = remote_lstat(sftp, path)

    if attributes is not None:
        if stat.S_ISLNK(attributes.st_mode):
            sftp.remove(path)
            sftp.mkdir(path)
        elif not stat.S_ISDIR(attributes.st_mode):
            sftp.remove(path)
            sftp.mkdir(path)
        return

    parent = posixpath.dirname(path)
    if parent and parent != path:
        ensure_remote_directory(sftp, parent)

    sftp.mkdir(path)


def remove_remote_tree(sftp: Any, path: str) -> None:
    attributes = sftp.lstat(path)

    if stat.S_ISLNK(attributes.st_mode):
        sftp.remove(path)
        return

    if stat.S_ISDIR(attributes.st_mode):
        for child in sftp.listdir_attr(path):
            remove_remote_tree(sftp, posixpath.join(path, child.filename))
        sftp.rmdir(path)
        return

    sftp.remove(path)


def upload_tree(
    sftp: Any,
    local_dir: Path,
    remote_dir: str,
    local_root: Path,
) -> None:
    ensure_remote_directory(sftp, remote_dir)

    for local_path in sorted(local_dir.iterdir(), key=lambda path: path.name):
        if local_path.is_symlink():
            fail(f"symlinks are not supported in deploy output: {local_path}")

        remote_path = posixpath.join(remote_dir, local_path.name)
        remote_attributes = remote_lstat(sftp, remote_path)

        if local_path.is_dir():
            if remote_attributes is not None and (
                stat.S_ISLNK(remote_attributes.st_mode)
                or not stat.S_ISDIR(remote_attributes.st_mode)
            ):
                if stat.S_ISDIR(remote_attributes.st_mode) and not stat.S_ISLNK(
                    remote_attributes.st_mode
                ):
                    remove_remote_tree(sftp, remote_path)
                else:
                    sftp.remove(remote_path)

            upload_tree(sftp, local_path, remote_path, local_root)
            continue

        if remote_attributes is not None:
            if stat.S_ISLNK(remote_attributes.st_mode):
                sftp.remove(remote_path)
            elif stat.S_ISDIR(remote_attributes.st_mode):
                remove_remote_tree(sftp, remote_path)

        relative_path = local_path.relative_to(local_root).as_posix()
        print(f"Uploading {relative_path}")
        sftp.put(str(local_path), remote_path)


def prune_remote_tree(sftp: Any, local_dir: Path, remote_dir: str, remote_root: str) -> None:
    local_names = {path.name for path in local_dir.iterdir()}

    for remote_item in sftp.listdir_attr(remote_dir):
        remote_path = posixpath.join(remote_dir, remote_item.filename)
        local_path = local_dir / remote_item.filename

        if remote_item.filename not in local_names:
            relative_path = remote_path.removeprefix(remote_root).lstrip("/")
            print(f"Removing stale {relative_path}")
            remove_remote_tree(sftp, remote_path)
            continue

        if stat.S_ISDIR(remote_item.st_mode) and local_path.is_dir():
            prune_remote_tree(sftp, local_path, remote_path, remote_root)


def main() -> None:
    try:
        import paramiko
    except ModuleNotFoundError:
        fail("Paramiko is not installed. Run npm run deploy:setup once, then retry.")

    config = load_configuration()
    temp_dir, local_root = extract_bundle()
    remote_root = config["remotePath"]

    print(f"Using SFTP config: {CONFIG_PATH}")
    print("Connecting to deployment server...")

    client = paramiko.SSHClient()
    client.load_system_host_keys()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

    try:
        client.connect(
            hostname=config["host"],
            port=config["port"],
            username=config["username"],
            password=config["password"],
            look_for_keys=False,
            allow_agent=False,
            timeout=30,
            auth_timeout=30,
            banner_timeout=30,
        )

        with client.open_sftp() as sftp:
            print("Uploading production theme build...")
            upload_tree(sftp, local_root, remote_root, local_root)
            prune_remote_tree(sftp, local_root, remote_root, remote_root)
    finally:
        client.close()
        temp_dir.cleanup()

    print("Deployment complete.")


if __name__ == "__main__":
    main()
