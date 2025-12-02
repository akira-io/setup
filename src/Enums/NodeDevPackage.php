<?php

declare(strict_types=1);

namespace Akira\Setup\Enums;

enum NodeDevPackage: string
{
    case RELEASE_IT = 'release-it';
    case COMMITLINT_CLI = '@commitlint/cli';
    case COMMITLINT_CONFIG = '@commitlint/config-conventional';
    case CONVENTIONAL_CHANGELOG = '@release-it/conventional-changelog';

    /**
     * @return array<string, string>
     */
    public static function toOptions(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return array<string>
     */
    public static function allValues(): array
    {
        return array_map(
            static fn (self $case): string => $case->value,
            self::cases()
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::RELEASE_IT => 'Release It',
            self::COMMITLINT_CLI => 'Commitlint CLI',
            self::COMMITLINT_CONFIG => 'Commitlint Config Conventional',
            self::CONVENTIONAL_CHANGELOG => 'Release It Conventional Changelog',
        };
    }
}
