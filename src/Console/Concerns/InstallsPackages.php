<?php

declare(strict_types=1);

namespace Akira\Setup\Console\Concerns;

use Akira\Setup\DTOs\PackageSelection;

use function Laravel\Prompts\warning;

trait InstallsPackages
{
    private function installPhpRequire(PackageSelection $selection): void
    {
        if ($selection->phpRequire === []) {
            return;
        }

        $success = $this->installPhpPackages->execute($selection->phpRequire);

        if (! $success) {
            warning('Failed to install some PHP packages.');
        }
    }

    private function installPhpRequireDev(PackageSelection $selection): void
    {
        if ($selection->phpRequireDev === []) {
            return;
        }

        $success = $this->installPhpDevPackages->execute($selection->phpRequireDev);

        if (! $success) {
            warning('Failed to install some PHP dev packages.');
        }
    }

    private function installNodePackages(PackageSelection $selection): void
    {
        if ($selection->nodeDevDependencies === []) {
            return;
        }

        $installCommand = $this->packageDetector->getInstallCommand($selection->nodePackageManager);

        $success = $this->installNodePackages->execute(
            $selection->nodeDevDependencies,
            $installCommand
        );

        if (! $success) {
            warning('Failed to install Node.js packages.');
        }
    }
}
