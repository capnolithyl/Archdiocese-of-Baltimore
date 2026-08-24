#!/usr/bin/env python3

from __future__ import annotations

import os
import posixpath
import stat
import sys
from pathlib import Path, PurePosixPath

import paramiko


LOCAL_ROOT = Path(os.environ.get("DEPLOY_LOCAL_PATH", "deploy/baltimore")).resolve()
REMOTE_ROOT_RAW = os.environ.get("DEPLOY_SFTP_PATH", "").rstrip("/")
REMOTE_ROOT = posixpath.normpath(REMOTE_ROOT_RAW) if REMOTE_ROOT_RAW else ""
HOST = os.environ.get("DEPLOY_SFTP_HOST", "")
PORT = int(os.environ.get("DEPLOY_SFTP_PORT") or "22")
USERNAME = os.environ.get("DEPLOY_SFTP_USER", "")
PASSWORD = os.environ.get("DEPLOY_SFTP_PASSWORD", "")


def fail(message: str) -> None:
    print(f"Deployment aborted: {message}", file=sys.stderr)
    raise SystemExit(1)


def validate_configuration() -> None:
    missing = [
        name
        for name, value in {
            "DEPLOY_SFTP_HOST": HOST,
            "DEPLOY_SFTP_USER": USERNAME,
            "DEPLOY_SFTP_PASSWORD": PASSWORD,
            "DEPLOY_SFTP_PATH": REMOTE_ROOT_RAW,
        }.items()
        if not value
    ]

    if missing:
        fail(f"missing required environment variable(s): {', '.join(missing)}")

    if not LOCAL_ROOT.is_dir():
        fail(f"local build directory does not exist: {LOCAL_ROOT}")

    if not REMOTE_ROOT_RAW.startswith("/"):
        fail("remote path must be absolute")

    if REMOTE_ROOT_RAW != REMOTE_ROOT:
        fail("remote path must be normalized and may not contain . or .. segments")

    remote_parts = PurePosixPath(REMOTE_ROOT).parts
    if tuple(remote_parts[-3:]) != ("wp-content", "themes", "baltimore"):
        fail("remote path must end exactly in /wp-content/themes/baltimore")


def remote_lstat(sftp: paramiko.SFTPClient, path: str):
    try:
        return sftp.lstat(path)
    except OSError:
        return None


def remote_exists(sftp: paramiko.SFTPClient, path: str) -> bool:
    return remote_lstat(sftp, path) is not None


def ensure_remote_directory(sftp: paramiko.SFTPClient, path: str) -> None:
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


def remove_remote_tree(sftp: paramiko.SFTPClient, path: str) -> None:
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


def upload_tree(sftp: paramiko.SFTPClient, local_dir: Path, remote_dir: str) -> None:
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
                if stat.S_ISDIR(remote_attributes.st_mode) and not stat.S_ISLNK(remote_attributes.st_mode):
                    remove_remote_tree(sftp, remote_path)
                else:
                    sftp.remove(remote_path)

            upload_tree(sftp, local_path, remote_path)
            continue

        if remote_attributes is not None:
            if stat.S_ISLNK(remote_attributes.st_mode):
                sftp.remove(remote_path)
            elif stat.S_ISDIR(remote_attributes.st_mode):
                remove_remote_tree(sftp, remote_path)

        relative_path = local_path.relative_to(LOCAL_ROOT).as_posix()
        print(f"Uploading {relative_path}")
        sftp.put(str(local_path), remote_path)


def prune_remote_tree(sftp: paramiko.SFTPClient, local_dir: Path, remote_dir: str) -> None:
    local_names = {path.name for path in local_dir.iterdir()}

    for remote_item in sftp.listdir_attr(remote_dir):
        remote_path = posixpath.join(remote_dir, remote_item.filename)
        local_path = local_dir / remote_item.filename

        if remote_item.filename not in local_names:
            print(f"Removing stale {remote_path.removeprefix(REMOTE_ROOT).lstrip('/')}")
            remove_remote_tree(sftp, remote_path)
            continue

        if stat.S_ISDIR(remote_item.st_mode) and local_path.is_dir():
            prune_remote_tree(sftp, local_path, remote_path)


def main() -> None:
    validate_configuration()

    print("Connecting to deployment server...")

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())

    try:
        client.connect(
            hostname=HOST,
            port=PORT,
            username=USERNAME,
            password=PASSWORD,
            look_for_keys=False,
            allow_agent=False,
            timeout=30,
            auth_timeout=30,
            banner_timeout=30,
        )

        with client.open_sftp() as sftp:
            print("Uploading production theme build...")
            upload_tree(sftp, LOCAL_ROOT, REMOTE_ROOT)
            prune_remote_tree(sftp, LOCAL_ROOT, REMOTE_ROOT)
    finally:
        client.close()

    print("Deployment complete.")


if __name__ == "__main__":
    main()
