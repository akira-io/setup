<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Actions\Concerns\HandlesPackageInstallation;
use Akira\Setup\Support\SkippedPackagesTracker;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final readonly class InstallPhpDevPackagesAction
{
    use HandlesPackageInstallation;

    /**
     * @param  array<string>  $requireDev
     */
    public function execute(array $requireDev): bool
    {
        if ($requireDev === []) {
            return true;
        }

        $packagesToInstall = $this->filterAlreadyInstalled($requireDev, 'require-dev');

        if ($packagesToInstall === []) {
            info('All dev packages are already installed.');

            return true;
        }

        info('Installing '.count($packagesToInstall).' dev packages...');

        foreach ($packagesToInstall as $package) {
            $process = new Process(
                ['composer', 'require', '--dev', $package],
                base_path(),
                null,
                null,
                600
            );

            info("Installing {$package}...");

            $process->run();

            if ($process->isSuccessful()) {
                info("✓ {$package} installed successfully");
            } else {
                warning("✗ Skipping {$package}");
                SkippedPackagesTracker::add($package, 'Installation failed');
            }
        }

        return true;
    }
}
