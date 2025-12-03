<?php

declare(strict_types=1);

use Akira\Setup\Actions\PublishConfigFilesAction;
use Akira\Setup\Actions\PublishWorkflowsAction;
use Akira\Setup\Console\Concerns\PublishesAssets;
use Akira\Setup\Support\FileManager;
use Illuminate\Console\Command;

it('addComposerScripts executes without error', function (): void {
    $command = new class extends Command
    {
        use PublishesAssets;

        /**
         * @var FileManager
         */
        public $fileManager;

        public function __construct()
        {
            parent::__construct();
            $this->fileManager = new FileManager();
        }

        public function testAddComposerScripts(): void
        {
            $this->addComposerScripts();
        }
    };

    $command->testAddComposerScripts();

    expect(true)->toBeTrue();
});

it('addPackageJsonScripts executes without error', function (): void {
    $command = new class extends Command
    {
        use PublishesAssets;

        /**
         * @var FileManager
         */
        public $fileManager;

        public function __construct()
        {
            parent::__construct();
            $this->fileManager = new FileManager();
        }

        public function testAddPackageJsonScripts(): void
        {
            $this->addPackageJsonScripts();
        }
    };

    $command->testAddPackageJsonScripts();

    expect(true)->toBeTrue();
});

it('publishConfigFiles executes without error', function (): void {
    $command = new class extends Command
    {
        use PublishesAssets;

        /**
         * @var PublishConfigFilesAction
         */
        public $publishConfigFiles;

        public function __construct()
        {
            parent::__construct();
            $this->publishConfigFiles = new PublishConfigFilesAction();
        }

        public function testPublishConfigFiles(): void
        {
            $this->publishConfigFiles();
        }
    };

    $command->testPublishConfigFiles();

    expect(true)->toBeTrue();
});

it('publishWorkflows executes without error', function (): void {
    $command = new class extends Command
    {
        use PublishesAssets;

        /**
         * @var PublishWorkflowsAction
         */
        public $publishWorkflows;

        public function __construct()
        {
            parent::__construct();
            $this->publishWorkflows = new PublishWorkflowsAction();
        }

        public function testPublishWorkflows(): void
        {
            $this->publishWorkflows();
        }
    };

    $command->testPublishWorkflows();

    expect(true)->toBeTrue();
});
