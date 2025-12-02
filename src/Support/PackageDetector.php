<?php

declare(strict_types=1);

namespace Akira\Setup\Support;

use Symfony\Component\Process\ExecutableFinder;

final readonly class PackageDetector
{
    public function __construct(
        private ExecutableFinder $executableFinder = new ExecutableFinder(),
    ) {}

    public function detectNodePackageManager(): string
    {
        $managers = ['bun', 'pnpm', 'yarn', 'npm'];

        foreach ($managers as $manager) {
            if ($this->executableFinder->find($manager) !== null) {
                return $manager;
            }
        }

        return 'npm';
    }

    public function getInstallCommand(string $manager): string
    {
        return match ($manager) {
            'bun' => 'bun add -d',
            'pnpm' => 'pnpm add -D',
            'yarn' => 'yarn add -D',
            default => 'npm install -D',
        };
    }

    public function hasComposerJson(): bool
    {
        return file_exists(base_path('composer.json'));
    }

    public function hasPackageJson(): bool
    {
        return file_exists(base_path('package.json'));
    }
}
