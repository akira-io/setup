<?php

declare(strict_types=1);

namespace Akira\Setup\Console\Concerns;

use Akira\Setup\DTOs\PackageSelection;

trait CalculatesSetupSteps
{
    /**
     * @return array<string>
     */
    private function calculateSteps(PackageSelection $selection): array
    {
        $steps = [];

        if ($selection->phpRequire !== []) {
            $steps[] = 'php-require';
        }

        if ($selection->phpRequireDev !== []) {
            $steps[] = 'php-require-dev';
        }

        if ($selection->nodeDevDependencies !== []) {
            $steps[] = 'node-packages';
        }

        $steps[] = 'composer-scripts';

        if ($this->packageDetector->hasPackageJson()) {
            $steps[] = 'package-json-scripts';
        }

        $steps[] = 'config-files';

        if ($selection->installWorkflows) {
            $steps[] = 'workflows';
        }

        return $steps;
    }
}
