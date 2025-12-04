<?php

declare(strict_types=1);

namespace Akira\Setup\Actions\Concerns;

trait HandlesPackageInstallation
{
    /**
     * @param  array<string>  $packages
     * @return array<string>
     */
    private function filterAlreadyInstalled(array $packages, string $section): array
    {
        $composerPath = base_path('composer.json');

        if (! file_exists($composerPath)) {
            return $packages;
        }

        $composerContent = file_get_contents($composerPath);

        if ($composerContent === false) {
            return $packages;
        }

        $composer = json_decode($composerContent, true);

        if (! is_array($composer) || ! isset($composer[$section]) || ! is_array($composer[$section])) {
            return $packages;
        }

        $installedPackages = array_keys($composer[$section]);

        return array_filter($packages, function (string $package) use ($installedPackages): bool {
            $packageName = explode(':', $package)[0];

            return ! in_array($packageName, $installedPackages, true);
        });
    }
}
