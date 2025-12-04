<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Actions\Concerns\HandlesPackageInstallation;
use Akira\Setup\Support\SkippedPackagesTracker;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

final readonly class InstallPhpPackagesAction
{
    use HandlesPackageInstallation;

    /**
     * @param  array<string>  $require
     */
    public function execute(array $require): bool
    {
        if ($require === []) {
            return true;
        }

        $packagesToInstall = $this->filterAlreadyInstalled($require, 'require');

        if ($packagesToInstall === []) {
            info('All packages are already installed.');

            return true;
        }

        info('Installing '.count($packagesToInstall).' packages...');

        $process = new Process(
            array_merge(['composer', 'require'], $packagesToInstall),
            base_path(),
            null,
            null,
            600
        );

        $process->run();

        if ($process->isSuccessful()) {
            info('✓ All packages installed successfully');

            return true;
        }

        warning('Batch installation failed. Trying one by one...');

        foreach ($packagesToInstall as $package) {
            $singleProcess = new Process(
                ['composer', 'require', $package],
                base_path(),
                null,
                null,
                600
            );

            $singleProcess->run();

            if ($singleProcess->isSuccessful()) {
                info("✓ {$package}");
            } else {
                warning("✗ {$package}");
                SkippedPackagesTracker::add($package, 'Installation failed');
            }
        }

        return true;
    }
}
