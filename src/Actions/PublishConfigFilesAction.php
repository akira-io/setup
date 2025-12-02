<?php

declare(strict_types=1);

namespace Akira\Setup\Actions;

use Akira\Setup\Support\FileManager;

final readonly class PublishConfigFilesAction
{
    public function __construct(
        private FileManager $fileManager = new FileManager(),
    ) {}

    public function execute(): bool
    {
        $files = [
            'pint.json' => 'pint.json',
            'rector.php' => 'rector.php',
            'release-it.json' => '.release-it.json',
        ];
        return array_all($files, fn(string $destination, string $stub): bool => $this->fileManager->copyStub($stub, $destination));
    }
}
