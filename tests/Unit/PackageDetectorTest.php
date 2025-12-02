<?php

declare(strict_types=1);

use Akira\Setup\Support\PackageDetector;

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
