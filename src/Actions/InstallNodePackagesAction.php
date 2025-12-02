<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Symfony\Component\Process\Process;

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

        $process->run();

        return $process->isSuccessful();
    }
}
