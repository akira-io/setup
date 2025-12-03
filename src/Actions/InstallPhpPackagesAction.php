<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Symfony\Component\Process\Process;

final readonly class InstallPhpPackagesAction
{
    /**
     * @param  array<string>  $require
     */
    public function execute(array $require): bool
    {
        if ($require !== []) {
            $process = new Process(
                array_merge(['composer', 'require'], $require),
                base_path(),
                null,
                null,
                600
            );

            $process->run();

            if (! $process->isSuccessful()) {
                return false;
            }
        }

        return true;
    }
}
