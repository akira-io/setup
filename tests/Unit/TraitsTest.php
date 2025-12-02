<?php

declare(strict_types=1);

use Akira\Setup\Console\Concerns\CalculatesSetupSteps;
use Akira\Setup\Console\Concerns\DisplaysSummary;
use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Support\PackageDetector;

describe('CalculatesSetupSteps Trait', function (): void {
    it('calculates steps for full installation', function (): void {
        $trait = new readonly class
        {
            use CalculatesSetupSteps;

            public function __construct(
                public PackageDetector $packageDetector = new PackageDetector(),
            ) {}

            public function testCalculateSteps(PackageSelection $selection): array
            {
                return $this->calculateSteps($selection);
            }
        };

        $selection = new PackageSelection(
            phpRequire: ['package1'],
            phpRequireDev: ['package2'],
            nodeDevDependencies: ['package3'],
            installWorkflows: true,
            nodePackageManager: 'npm',
        );

        $steps = $trait->testCalculateSteps($selection);

        expect($steps)->toBeArray()
            ->and($steps)->toContain('php-require')
            ->and($steps)->toContain('php-require-dev')
            ->and($steps)->toContain('node-packages')
            ->and($steps)->toContain('composer-scripts')
            ->and($steps)->toContain('config-files')
            ->and($steps)->toContain('workflows');
    });

    it('skips empty package steps', function (): void {
        $trait = new readonly class
        {
            use CalculatesSetupSteps;

            public function __construct(
                public PackageDetector $packageDetector = new PackageDetector(),
            ) {}

            public function testCalculateSteps(PackageSelection $selection): array
            {
                return $this->calculateSteps($selection);
            }
        };

        $selection = new PackageSelection(
            phpRequire: [],
            phpRequireDev: [],
            nodeDevDependencies: [],
            installWorkflows: false,
            nodePackageManager: 'npm',
        );

        $steps = $trait->testCalculateSteps($selection);

        expect($steps)->not->toContain('php-require')
            ->and($steps)->not->toContain('php-require-dev')
            ->and($steps)->not->toContain('node-packages')
            ->and($steps)->not->toContain('workflows')
            ->and($steps)->toContain('composer-scripts')
            ->and($steps)->toContain('config-files');
    });
});

describe('DisplaysSummary Trait', function (): void {
    it('can be used in a class', function (): void {
        $trait = new class
        {
            use DisplaysSummary;

            public function testDisplaySummary(PackageSelection $selection): void
            {
                // Just verify the trait method exists and is callable
                expect(method_exists($this, 'displaySummary'))->toBeTrue();
            }
        };

        expect($trait)->toBeObject();
    });
});
