<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Support\FileManager;

final readonly class PublishWorkflowsAction
{
    public function __construct(
        private FileManager $fileManager = new FileManager(),
    ) {}

    public function execute(): bool
    {
        $workflows = [
            'github/release-discord.yml' => '.github/workflows/release-discord.yml',
            'github/tests.yml' => '.github/workflows/tests.yml',
        ];
        return array_all($workflows, fn(string $destination, string $stub): bool => $this->fileManager->copyStub($stub, $destination));
    }
}
