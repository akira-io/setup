<?php

declare(strict_types=1);

use Akira\Setup\Enums\NodeDevPackage;
use Akira\Setup\Enums\PhpDevPackage;
use Akira\Setup\Enums\PhpPackage;

describe('PhpPackage Enum', function (): void {
    it('has correct values', function (): void {
        expect(PhpPackage::NUNOMADURO_ESSENTIALS->value)->toBe('nunomaduro/essentials')
            ->and(PhpPackage::LARAVEL_AUTH_LOGS->value)->toBe('akira/laravel-auth-logs');
    });

    it('has correct labels', function (): void {
        expect(PhpPackage::NUNOMADURO_ESSENTIALS->label())->toBe('Nunomaduro Essentials')
            ->and(PhpPackage::LARAVEL_AUTH_LOGS->label())->toBe('Akira Laravel Auth Logs');
    });

    it('converts to options array', function (): void {
        $options = PhpPackage::toOptions();

        expect($options)->toBeArray()
            ->and($options)->toHaveKey('nunomaduro/essentials')
            ->and($options)->toHaveKey('akira/laravel-auth-logs')
            ->and($options['nunomaduro/essentials'])->toBe('Nunomaduro Essentials');
    });

    it('returns all values', function (): void {
        $values = PhpPackage::allValues();

        expect($values)->toBeArray()
            ->and($values)->toContain('nunomaduro/essentials')
            ->and($values)->toContain('akira/laravel-auth-logs')
            ->and($values)->toContain('spatie/laravel-route-attributes')
            ->and($values)->toHaveCount(3);
    });
});

describe('PhpDevPackage Enum', function (): void {
    it('has correct values', function (): void {
        expect(PhpDevPackage::PEST->value)->toBe('pestphp/pest')
            ->and(PhpDevPackage::LARAVEL_PINT->value)->toBe('laravel/pint')
            ->and(PhpDevPackage::RECTOR->value)->toBe('rector/rector');
    });

    it('has correct labels', function (): void {
        expect(PhpDevPackage::PEST->label())->toBe('Pest')
            ->and(PhpDevPackage::LARAVEL_PINT->label())->toBe('Laravel Pint')
            ->and(PhpDevPackage::LARASTAN->label())->toBe('Larastan');
    });

    it('converts to options array', function (): void {
        $options = PhpDevPackage::toOptions();

        expect($options)->toBeArray()
            ->and($options)->toHaveKey('pestphp/pest')
            ->and($options)->toHaveKey('laravel/pint')
            ->and($options['pestphp/pest'])->toBe('Pest');
    });

    it('returns all values', function (): void {
        $values = PhpDevPackage::allValues();

        expect($values)->toBeArray()
            ->and($values)->toContain('pestphp/pest')
            ->and($values)->toContain('laravel/pint')
            ->and($values)->toHaveCount(16);
    });

    it('includes all expected packages', function (): void {
        $values = PhpDevPackage::allValues();

        expect($values)->toContain('akira/laravel-debugger')
            ->and($values)->toContain('driftingly/rector-laravel')
            ->and($values)->toContain('fakerphp/faker')
            ->and($values)->toContain('larastan/larastan')
            ->and($values)->toContain('roave/security-advisories')
            ->and($values)->toContain('barryvdh/laravel-debugbar');

    });
});

describe('NodeDevPackage Enum', function (): void {
    it('has correct values', function (): void {
        expect(NodeDevPackage::RELEASE_IT->value)->toBe('release-it')
            ->and(NodeDevPackage::COMMITLINT_CLI->value)->toBe('@commitlint/cli')
            ->and(NodeDevPackage::COMMITLINT_CONFIG->value)->toBe('@commitlint/config-conventional')
            ->and(NodeDevPackage::CONVENTIONAL_CHANGELOG->value)->toBe('@release-it/conventional-changelog');
    });

    it('has correct labels', function (): void {
        expect(NodeDevPackage::RELEASE_IT->label())->toBe('Release It')
            ->and(NodeDevPackage::COMMITLINT_CLI->label())->toBe('Commitlint CLI')
            ->and(NodeDevPackage::COMMITLINT_CONFIG->label())->toBe('Commitlint Config Conventional')
            ->and(NodeDevPackage::CONVENTIONAL_CHANGELOG->label())->toBe('Release It Conventional Changelog');
    });

    it('converts to options array', function (): void {
        $options = NodeDevPackage::toOptions();

        expect($options)->toBeArray()
            ->and($options)->toHaveKey('release-it')
            ->and($options)->toHaveKey('@commitlint/cli')
            ->and($options['release-it'])->toBe('Release It');
    });

    it('returns all values', function (): void {
        $values = NodeDevPackage::allValues();

        expect($values)->toBeArray()
            ->and($values)->toContain('release-it')
            ->and($values)->toContain('@commitlint/cli')
            ->and($values)->toHaveCount(4);
    });
});
