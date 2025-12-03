<?php

declare(strict_types=1);

namespace Akira\Setup\Console\Concerns;

use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Enums\NodeDevPackage;
use Akira\Setup\Enums\PhpDevPackage;
use Akira\Setup\Enums\PhpPackage;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;

trait CollectsUserChoices
{
    private function collectUserChoices(): PackageSelection
    {
        note('Select the packages and features you want to install.');

        /** @var array<string> $phpRequire */
        $phpRequire = multiselect(
            label: 'Select PHP packages to require:',
            options: PhpPackage::toOptions(),
            default: PhpPackage::allValues(),
            hint: 'Use space to select, Ctrl+A to toggle all, Enter to confirm',
        );

        /** @var array<string> $phpRequireDev */
        $phpRequireDev = multiselect(
            label: 'Select PHP dev packages to require:',
            options: PhpDevPackage::toOptions(),
            default: PhpDevPackage::allValues(),
            hint: 'Use space to select, Ctrl+A to toggle all, Enter to confirm',
        );

        /** @var array<string> $nodeDevDependencies */
        $nodeDevDependencies = [];
        $nodePackageManager = 'npm';

        if ($this->packageDetector->hasPackageJson()) {
            /** @var array<string> $nodeDevDependencies */
            $nodeDevDependencies = multiselect(
                label: 'Select Node.js dev dependencies:',
                options: NodeDevPackage::toOptions(),
                default: NodeDevPackage::allValues(),
                hint: 'Use space to select, Ctrl+A to toggle all, Enter to confirm',
            );

            $detectedManager = $this->packageDetector->detectNodePackageManager();

            $nodePackageManager = (string) select(
                label: 'Select Node package manager:',
                options: ['npm', 'pnpm', 'yarn', 'bun'],
                default: $detectedManager,
            );
        }

        $installWorkflows = confirm(
            label: 'Install GitHub Actions workflows?',
            default: true,
        );

        return new PackageSelection(
            phpRequire: $phpRequire,
            phpRequireDev: $phpRequireDev,
            nodeDevDependencies: $nodeDevDependencies,
            installWorkflows: $installWorkflows,
            nodePackageManager: $nodePackageManager,
        );
    }
}
