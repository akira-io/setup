<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Support\SkippedPackagesTracker;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\info;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

final readonly class InstallPhpDevPackagesAction
{
    /**
     * @param  array<string>  $requireDev
     */
    public function execute(array $requireDev): bool
    {
        if ($requireDev === []) {
            return true;
        }

        $remainingPackages = $requireDev;
        $attempts = 0;
        $maxAttempts = 3;

        while ($remainingPackages !== [] && $attempts < $maxAttempts) {
            $attempts++;
            $output = '';
            $errorOutput = '';

            info('Installing: '.implode(', ', $remainingPackages));

            $result = spin(
                callback: function () use ($remainingPackages, &$output, &$errorOutput): bool {
                    $process = new Process(
                        array_merge(['composer', 'require', '--dev'], $remainingPackages),
                        base_path(),
                        null,
                        null,
                        600
                    );

                    $process->run(function ($type, $buffer) use (&$output, &$errorOutput): void {
                        if ($type === Process::ERR) {
                            $errorOutput .= $buffer;
                        } else {
                            $output .= $buffer;
                        }
                    });

                    return $process->isSuccessful();
                },
                message: 'Installing dev packages...'
            );

            if ($result) {
                return true;
            }

            $problematicPackages = $this->parseAndTrackErrors($errorOutput ?: $output, $remainingPackages);

            if ($problematicPackages === []) {
                warning('Failed to install dev packages. No stability issues detected.');
                warning('Error output: '.mb_substr($errorOutput ?: $output, 0, 500));

                return false;
            }

            foreach ($problematicPackages as $pkg) {
                warning("Skipping {$pkg} due to stability constraints. Retrying without it...");
            }

            $remainingPackages = array_diff($remainingPackages, $problematicPackages);
        }

        return $remainingPackages === [];
    }

    /**
     * @param  array<string>  $requestedPackages
     * @return array<string>
     */
    private function parseAndTrackErrors(string $output, array $requestedPackages): array
    {
        $problematicPackages = [];

        if (str_contains($output, 'minimum-stability') ||
            str_contains($output, 'stability flag') ||
            str_contains($output, 'requires a stability flag') ||
            str_contains($output, 'no matching package found')) {

            foreach ($requestedPackages as $package) {
                $packageName = explode(':', $package)[0];
                if (str_contains($output, $packageName)) {
                    SkippedPackagesTracker::add($package, 'Stability constraint');
                    $problematicPackages[] = $package;
                }
            }
        }

        return $problematicPackages;
    }
}
