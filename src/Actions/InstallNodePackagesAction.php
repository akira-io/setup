<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Symfony\Component\Process\Process;

use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

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

        /** @var array<int|string, string> $commandParts */
        $commandParts = array_merge(
            explode(' ', $installCommand),
            $packages
        );

        $result = spin(
            callback: function () use ($commandParts, &$output, &$errorOutput): bool {
                $process = new Process(
                    $commandParts,
                    base_path(),
                    null,
                    null,
                    600
                );

                $process->run(function (string $type, string $buffer) use (&$output, &$errorOutput): void {
                    if ($type === Process::ERR) {
                        $errorOutput .= $buffer;
                    } else {
                        $output .= $buffer;
                    }
                });

                return $process->isSuccessful();
            },
            message: 'Installing Node packages: '.implode(', ', $packages)
        );

        if (! $result) {
            $this->displayError($errorOutput ?: $output);

            return false;
        }

        return true;
    }

    private function displayError(string $output): void
    {
        $lines = explode("\n", mb_trim($output));
        /** @var array<string> $relevantLines */
        $relevantLines = [];

        foreach ($lines as $line) {
            /** @var string $line */
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
            foreach ($relevantLines as $relevantLine) {
                error($relevantLine);
            }
        } else {
            error('Failed to install Node packages.');
        }
    }
}
