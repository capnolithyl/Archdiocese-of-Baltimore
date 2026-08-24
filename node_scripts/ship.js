#!/usr/bin/env node

import { execSync } from 'node:child_process';

function run(command) {
	console.log(`\n> ${command}`);
	execSync(command, {
		stdio: 'inherit',
		shell: true,
	});
}

function output(command) {
	return execSync(command, {
		encoding: 'utf8',
		stdio: ['ignore', 'pipe', 'pipe'],
		shell: true,
	}).trim();
}

function fail(message) {
	console.error(`\nShip aborted: ${message}`);
	process.exit(1);
}

const branch = output('git branch --show-current');
if (branch !== 'main') {
	fail(`current branch is "${branch || '(detached HEAD)'}"; switch to main first`);
}

const status = output('git status --porcelain');
if (status) {
	fail('working tree is not clean; commit or stash your changes first');
}

console.log('Shipping Baltimore theme from clean main branch.');

try {
	// Prove the exact production bundle can be built before changing anything remote.
	run('npm run bundle');

	// Do not deploy if GitHub rejects the push for any reason.
	run('git push origin main');

	// Upload the already-built bundle from this machine, whose IP is whitelisted.
	run('npm run deploy:upload');
} catch (error) {
	fail('a build, push, or deployment command failed; see the output above');
}

console.log('\nShip complete.');
