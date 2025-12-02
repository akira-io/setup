<?php

declare(strict_types=1);

namespace Akira\Setup\Enums;

enum PhpPackage: string
{
    case NUNOMADURO_ESSENTIALS = 'nunomaduro/essentials';
    case LARAVEL_AUTH_LOGS = 'akira/laravel-auth-logs';

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
            self::NUNOMADURO_ESSENTIALS => 'Nunomaduro Essentials',
            self::LARAVEL_AUTH_LOGS => 'Akira Laravel Auth Logs',
        };
    }
}
