<?php

declare(strict_types=1);

use Akira\Setup\Actions\InstallNodePackagesAction;

it('installs node packages successfully', function (): void {
    $action = new InstallNodePackagesAction();
    $packages = ['release-it'];
    $result = $action->execute($packages, 'npm install -D');
    expect($result)->toBeBool();
});

it('handles empty package list', function (): void {
    $action = new InstallNodePackagesAction();
    $result = $action->execute([], 'npm install -D');
    expect($result)->toBeBool();
});
