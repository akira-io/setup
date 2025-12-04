<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Support\SkippedPackagesTracker;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final readonly class InstallPhpPackagesAction
{
    /**
     * @param  array<string>  $require
     */
    public function execute(array $require): bool
    {
        if ($require === []) {
            return true;
        }

        info('Installing packages: '.implode(', ', $require));

        $installedCount = 0;

        foreach ($require as $package) {
            $output = '';
            $errorOutput = '';

            $process = new Process(
                ['composer', 'require', $package],
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

            if (! $process->isSuccessful()) {
                if ($this->isStabilityIssue($errorOutput ?: $output)) {
                    SkippedPackagesTracker::add($package, 'Stability constraint');
                } else {
                    $this->displayError($errorOutput ?: $output, $package);
                }
            } else {
                $installedCount++;
            }
        }

        return $installedCount > 0;
    }

    private function isStabilityIssue(string $output): bool
    {
        return str_contains($output, 'minimum-stability') || str_contains($output, 'stability flag');
    }

    private function displayError(string $output, string $package): void
    {
        $lines = explode("\n", mb_trim($output));
        $relevantLines = [];

        foreach ($lines as $line) {
            $line = mb_trim($line);
            if ($line === '') {
                continue;
            }
            if (str_starts_with($line, 'Reading ')) {
                continue;
            }
            if (str_starts_with($line, 'Loading ')) {
                continue;
            }

            if (str_contains($line, 'Problem') || str_contains($line, 'conflict') || str_contains($line, 'requires')) {
                $relevantLines[] = $line;
            }
        }

        if ($relevantLines !== []) {
            error("Failed to install {$package}:");
            foreach ($relevantLines as $line) {
                error("  {$line}");
            }
        } else {
            error("Failed to install {$package}. Check your composer.json for conflicts.");
        }
    }
}
