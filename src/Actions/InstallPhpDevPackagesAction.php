<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Symfony\Component\Process\Process;

final readonly class InstallPhpDevPackagesAction
{
    /**
     * @param  array<string>  $requireDev
     */
    public function execute(array $requireDev): bool
    {

        $process = new Process(
            array_merge(['composer', 'require', '--dev'], $requireDev),
            base_path(),
            null,
            null,
            600
        );

        $process->run();

        return $process->isSuccessful();
    }
}
