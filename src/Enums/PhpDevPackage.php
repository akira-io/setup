<?php

declare(strict_types=1);

namespace Akira\Setup\Enums;

enum PhpDevPackage: string
{
    case LARAVEL_DEBUGGER = 'akira/laravel-debugger';
    case RECTOR_LARAVEL = 'driftingly/rector-laravel';
    case FAKER = 'fakerphp/faker';
    case LARASTAN = 'larastan/larastan';
    case LARAVEL_BOOST = 'laravel/boost';
    case LARAVEL_PAIL = 'laravel/pail';
    case LARAVEL_PINT = 'laravel/pint';
    case MOCKERY = 'mockery/mockery';
    case COLLISION = 'nunomaduro/collision';
    case PEST = 'pestphp/pest';
    case PEST_PLUGIN_BROWSER = 'pestphp/pest-plugin-browser';
    case PEST_PLUGIN_LARAVEL = 'pestphp/pest-plugin-laravel';
    case PEST_PLUGIN_TYPE_COVERAGE = 'pestphp/pest-plugin-type-coverage';
    case RECTOR = 'rector/rector';
    case SECURITY_ADVISORIES = 'roave/security-advisories';
    case LARAVEL_DEBUG_BAR = 'barryvdh/laravel-debugbar';

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
            self::LARAVEL_DEBUGGER => 'Akira Laravel Debugger',
            self::RECTOR_LARAVEL => 'Rector Laravel',
            self::FAKER => 'Faker PHP',
            self::LARASTAN => 'Larastan',
            self::LARAVEL_BOOST => 'Laravel Boost',
            self::LARAVEL_PAIL => 'Laravel Pail',
            self::LARAVEL_PINT => 'Laravel Pint',
            self::MOCKERY => 'Mockery',
            self::COLLISION => 'Collision',
            self::PEST => 'Pest',
            self::PEST_PLUGIN_BROWSER => 'Pest Plugin Browser',
            self::PEST_PLUGIN_LARAVEL => 'Pest Plugin Laravel',
            self::PEST_PLUGIN_TYPE_COVERAGE => 'Pest Plugin Type Coverage',
            self::RECTOR => 'Rector',
            self::SECURITY_ADVISORIES => 'Roave Security Advisories',
            self::LARAVEL_DEBUG_BAR => 'Laravel Debug Bar',
        };
    }
}
