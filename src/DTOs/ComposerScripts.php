<?php

declare(strict_types=1);

namespace Akira\Setup\DTOs;

final readonly class ComposerScripts
{
    /**
     * @return array<string, string|array<string>>
     */
    public static function getScripts(): array
    {
        return [
            'lint' => [
                'rector',
                'pint --parallel',
                'npm run lint',
            ],
            'test:type-coverage' => 'pest --type-coverage --min=100',
            'test:lint' => [
                'pint --parallel --test',
                'rector --dry-run',
                'npm run test:lint',
            ],
            'test:unit' => 'pest --parallel --coverage --exactly=100.0',
            'test:types' => [
                'phpstan',
                'npm run test:types',
            ],
            'test' => [
                '@test:type-coverage',
                '@test:unit',
                '@test:lint',
                '@test:types',
            ],
        ];
    }
}
