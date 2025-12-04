<?php

declare(strict_types=1);

namespace Akira\Setup\Support;

final class SkippedPackagesTracker
{
    /**
     * @var array<string, string>
     */
    private static array $skippedPackages = [];

    public static function add(string $package, string $reason): void
    {
        self::$skippedPackages[$package] = $reason;
    }

    /**
     * @return array<string, string>
     */
    public static function get(): array
    {
        return self::$skippedPackages;
    }

    public static function clear(): void
    {
        self::$skippedPackages = [];
    }

    public static function hasSkipped(): bool
    {
        return self::$skippedPackages !== [];
    }
}
