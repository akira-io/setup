<?php

declare(strict_types=1);

namespace Akira\Setup\Support;

use Illuminate\Filesystem\Filesystem;

final readonly class FileManager
{
    public function __construct(
        private Filesystem $files = new Filesystem(),
    ) {}

    public function copyStub(string $stub, string $destination): bool
    {
        $stubPath = __DIR__.'/../../stubs/'.$stub;

        if (! $this->files->exists($stubPath)) {
            return false;
        }

        $destinationPath = base_path($destination);
        $this->files->ensureDirectoryExists(dirname($destinationPath));

        return $this->files->copy($stubPath, $destinationPath);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateJson(string $path, array $data): bool
    {
        $fullPath = base_path($path);

        if (! $this->files->exists($fullPath)) {
            return false;
        }

        $current = json_decode($this->files->get($fullPath), true);

        if (! is_array($current)) {
            return false;
        }

        $merged = array_merge_recursive($current, $data);

        return $this->files->put(
            $fullPath,
            json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        ) !== false;
    }

    /**
     * @param  array<string, mixed>  $scripts
     */
    public function mergeComposerScripts(array $scripts): bool
    {
        $composerPath = base_path('composer.json');

        if (! $this->files->exists($composerPath)) {
            return false;
        }

        $composerContent = json_decode($this->files->get($composerPath), true);

        if (! is_array($composerContent)) {
            return false;
        }

        /** @var array<string, mixed> $composer */
        $composer = $composerContent;

        if (! isset($composer['scripts']) || ! is_array($composer['scripts'])) {
            $composer['scripts'] = [];
        }

        /** @var array<string, mixed> $composerScripts */
        $composerScripts = $composer['scripts'];

        foreach ($scripts as $key => $value) {
            if (isset($composerScripts[$key])) {
                $existingValue = $composerScripts[$key];

                if (is_array($value) && is_array($existingValue)) {
                    $composerScripts[$key] = array_unique(
                        array_merge($existingValue, $value)
                    );
                } elseif (is_string($value) && is_string($existingValue)) {
                    if ($existingValue !== $value) {
                        $composerScripts[$key] = [$existingValue, $value];
                    }
                }
            } else {
                $composerScripts[$key] = $value;
            }
        }

        $composer['scripts'] = $composerScripts;

        return $this->files->put(
            $composerPath,
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        ) !== false;
    }

    /**
     * @param  array<string, string>  $scripts
     */
    public function addPackageJsonScripts(array $scripts): bool
    {
        $packagePath = base_path('package.json');

        if (! $this->files->exists($packagePath)) {
            return false;
        }

        $packageContent = json_decode($this->files->get($packagePath), true);

        if (! is_array($packageContent)) {
            return false;
        }

        /** @var array<string, mixed> $package */
        $package = $packageContent;

        if (! isset($package['scripts']) || ! is_array($package['scripts'])) {
            $package['scripts'] = [];
        }

        /** @var array<string, mixed> $packageScripts */
        $packageScripts = $package['scripts'];

        $package['scripts'] = array_merge($packageScripts, $scripts);

        return $this->files->put(
            $packagePath,
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)."\n"
        ) !== false;
    }
}
