<?php

declare(strict_types=1);

use Akira\Setup\Support\PackageDetector;
use Symfony\Component\Process\ExecutableFinder;

it('detects node package manager', function (): void {
    $detector = new PackageDetector();
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBeIn(['npm', 'pnpm', 'yarn', 'bun']);
});

it('returns correct install command for each package manager', function (string $manager, string $expected): void {
    $detector = new PackageDetector();
    $command = $detector->getInstallCommand($manager);

    expect($command)->toBe($expected);
})->with([
    ['bun', 'bun add -d'],
    ['pnpm', 'pnpm add -D'],
    ['yarn', 'yarn add -D'],
    ['npm', 'npm install -D'],
]);

it('checks for composer.json existence', function (): void {
    $detector = new PackageDetector();

    expect($detector->hasComposerJson())->toBeBool();
});

it('checks for package.json existence', function (): void {
    $detector = new PackageDetector();

    expect($detector->hasPackageJson())->toBeBool();
});

it('returns npm as fallback package manager', function (): void {
    $detector = new PackageDetector();
    $manager = $detector->detectNodePackageManager();

    // Will be one of the supported managers, npm as fallback
    expect($manager)->toBeIn(['npm', 'pnpm', 'yarn', 'bun']);
});

it('returns npm when no package manager is found', function (): void {
    // Create a custom ExecutableFinder that returns null for everything
    $executableFinder = new class extends ExecutableFinder
    {
        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return null; // Simulate no package manager found
        }
    };

    $detector = new PackageDetector($executableFinder);
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBe('npm');
});

it('detects bun when available', function (): void {
    $executableFinder = new class extends ExecutableFinder
    {
        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $name === 'bun' ? '/usr/bin/bun' : null;
        }
    };

    $detector = new PackageDetector($executableFinder);
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBe('bun');
});

it('detects pnpm when bun not available', function (): void {
    $executableFinder = new class extends ExecutableFinder
    {
        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $name === 'pnpm' ? '/usr/bin/pnpm' : null;
        }
    };

    $detector = new PackageDetector($executableFinder);
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBe('pnpm');
});

it('detects yarn when bun and pnpm not available', function (): void {
    $executableFinder = new class extends ExecutableFinder
    {
        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $name === 'yarn' ? '/usr/bin/yarn' : null;
        }
    };

    $detector = new PackageDetector($executableFinder);
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBe('yarn');
});

it('detects npm when only npm available', function (): void {
    $executableFinder = new class extends ExecutableFinder
    {
        public function find(string $name, ?string $default = null, array $extraDirs = []): ?string
        {
            return $name === 'npm' ? '/usr/bin/npm' : null;
        }
    };

    $detector = new PackageDetector($executableFinder);
    $manager = $detector->detectNodePackageManager();

    expect($manager)->toBe('npm');
});

it('can be instantiated with custom ExecutableFinder', function (): void {
    $executableFinder = new ExecutableFinder();
    $detector = new PackageDetector($executableFinder);

    expect($detector)->toBeInstanceOf(PackageDetector::class);
});

it('returns correct command for default case in match', function (): void {
    $detector = new PackageDetector();

    // Test with an unknown manager to hit the default case
    $command = $detector->getInstallCommand('unknown-manager');

    expect($command)->toBe('npm install -D');
});
