<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Symfony\Component\Process\Process;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

final readonly class InstallNodePackagesAction
{
    /**
     * @param  array<string>  $packages
     */
    public function execute(array $packages, string $installCommand): bool
    {
        if ($packages === []) {
            return true;
        }

        $output = '';
        $errorOutput = '';

        info('Installing Node packages: '.implode(', ', $packages));

        $commandParts = array_merge(
            explode(' ', $installCommand),
            $packages
        );

        $process = new Process(
            $commandParts,
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
            $this->displayError($errorOutput ?: $output);

            return false;
        }

        return true;
    }

    private function displayError(string $output): void
    {
        $lines = explode("\n", mb_trim($output));
        $relevantLines = [];

        foreach ($lines as $line) {
            $line = mb_trim($line);
            if ($line === '') {
                continue;
            }
            if (str_starts_with($line, 'npm ')) {
                continue;
            }
            if (str_starts_with($line, 'added ')) {
                continue;
            }

            if (str_contains($line, 'ERR!') || str_contains($line, 'error') || str_contains($line, 'WARN')) {
                $relevantLines[] = $line;
            }
        }

        if ($relevantLines !== []) {
            foreach ($relevantLines as $line) {
                error($line);
            }
        } else {
            error('Failed to install Node packages.');
        }
    }
}
