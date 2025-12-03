<?php

declare(strict_types=1);

use Akira\Setup\Console\Concerns\CollectsUserChoices;
use Akira\Setup\Console\Concerns\DisplaysSummary;
use Akira\Setup\Console\Concerns\InstallsPackages;
use Akira\Setup\Console\Concerns\PublishesAssets;

it('CollectsUserChoices trait has method', function (): void {
    $trait = new class
    {
        use CollectsUserChoices;

        public function test(): bool
        {
            return method_exists($this, 'collectUserChoices');
        }
    };
    expect($trait->test())->toBeTrue();
});

it('InstallsPackages trait has methods', function (): void {
    $trait = new class
    {
        use InstallsPackages;

        public function test(): bool
        {
            return method_exists($this, 'installPhpRequire')
                && method_exists($this, 'installPhpRequireDev')
                && method_exists($this, 'installNodePackages');
        }
    };
    expect($trait->test())->toBeTrue();
});

it('PublishesAssets trait has methods', function (): void {
    $trait = new class
    {
        use PublishesAssets;

        public function test(): bool
        {
            return method_exists($this, 'addComposerScripts')
                && method_exists($this, 'publishConfigFiles');
        }
    };
    expect($trait->test())->toBeTrue();
});

it('DisplaysSummary trait has method', function (): void {
    $trait = new class
    {
        use DisplaysSummary;

        public function test(): bool
        {
            return method_exists($this, 'displaySummary');
        }
    };
    expect($trait->test())->toBeTrue();
});
