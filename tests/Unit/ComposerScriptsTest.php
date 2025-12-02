<?php

declare(strict_types=1);

use Akira\Setup\DTOs\ComposerScripts;

it('returns composer scripts array', function (): void {
    $scripts = ComposerScripts::getScripts();

    expect($scripts)->toBeArray()
        ->and($scripts)->toHaveKeys([
            'lint',
            'test:type-coverage',
            'test:lint',
            'test:unit',
            'test:types',
            'test',
        ]);
});

it('has correct test script structure', function (): void {
    $scripts = ComposerScripts::getScripts();

    expect($scripts['test'])->toBeArray()
        ->and($scripts['test'])->toContain('@test:type-coverage')
        ->and($scripts['test'])->toContain('@test:unit')
        ->and($scripts['test'])->toContain('@test:lint')
        ->and($scripts['test'])->toContain('@test:types');
});

it('has correct lint script structure', function (): void {
    $scripts = ComposerScripts::getScripts();

    expect($scripts['lint'])->toBeArray()
        ->and($scripts['lint'])->toContain('rector')
        ->and($scripts['lint'])->toContain('pint --parallel')
        ->and($scripts['lint'])->toContain('npm run lint');
});
