<?php

declare(strict_types=1);

namespace Akira\Setup\Console\Concerns;

use Akira\Setup\DTOs\ComposerScripts;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

trait PublishesAssets
{
    private function addComposerScripts(): void
    {
        info('Adding Composer scripts...');

        $scripts = ComposerScripts::getScripts();

        $success = $this->fileManager->mergeComposerScripts($scripts);

        if (! $success) {
            warning('Failed to merge Composer scripts.');
        }
    }

    private function addPackageJsonScripts(): void
    {
        info('Adding package.json scripts...');

        $scripts = [
            'release' => 'release-it',
        ];

        $success = $this->fileManager->addPackageJsonScripts($scripts);

        if (! $success) {
            warning('Failed to add package.json scripts.');
        }
    }

    private function publishConfigFiles(): void
    {
        info('Publishing configuration files...');

        $success = $this->publishConfigFiles->execute();

        if (! $success) {
            warning('Failed to publish some configuration files.');
        }
    }

    private function publishWorkflows(): void
    {
        info('Publishing GitHub workflows...');

        $success = $this->publishWorkflows->execute();

        if (! $success) {
            warning('Failed to publish GitHub workflows.');
        }
    }
}
