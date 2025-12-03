<?php

declare(strict_types=1);

use Akira\Setup\Actions\InstallPhpPackagesAction;

it('installs php packages', function (): void {
    $action = new InstallPhpPackagesAction();
    $result = $action->execute(['vendor/package'], ['vendor/dev']);
    expect($result)->toBeBool();
});

it('handles empty lists', function (): void {
    $action = new InstallPhpPackagesAction();
    $result = $action->execute([], []);
    expect($result)->toBeBool();
});

it('installs only require packages', function (): void {
    $action = new InstallPhpPackagesAction();
    $result = $action->execute(['vendor/package'], []);
    expect($result)->toBeBool();
});

it('installs only require-dev packages', function (): void {
    $action = new InstallPhpPackagesAction();
    $result = $action->execute([], ['vendor/dev-package']);
    expect($result)->toBeBool();
});

it('handles mixed packages', function (): void {
    $action = new InstallPhpPackagesAction();
    $result = $action->execute(['vendor/prod'], ['vendor/dev']);
    expect($result)->toBeBool();
});
