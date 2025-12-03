<?php

declare(strict_types=1);

namespace Akira\Setup\Console;

use Akira\Setup\Actions\InstallNodePackagesAction;
use Akira\Setup\Actions\InstallPhpDevPackagesAction;
use Akira\Setup\Actions\InstallPhpPackagesAction;
use Akira\Setup\Actions\PublishConfigFilesAction;
use Akira\Setup\Actions\PublishWorkflowsAction;
use Akira\Setup\Console\Concerns\CalculatesSetupSteps;
use Akira\Setup\Console\Concerns\CollectsUserChoices;
use Akira\Setup\Console\Concerns\DisplaysSummary;
use Akira\Setup\Console\Concerns\InstallsPackages;
use Akira\Setup\Console\Concerns\PublishesAssets;
use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Support\FileManager;
use Akira\Setup\Support\PackageDetector;
use Illuminate\Console\Command;

use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\warning;

final class SetupCommand extends Command
{
    use CalculatesSetupSteps;
    use CollectsUserChoices;
    use DisplaysSummary;
    use InstallsPackages;
    use PublishesAssets;

    protected $signature = 'akira:setup';

    protected $description = 'Interactive Laravel project setup with best practices and tools';

    public function __construct(
        private readonly PackageDetector $packageDetector,
        private readonly FileManager $fileManager,
        private readonly InstallPhpPackagesAction $installPhpPackages,
        private readonly InstallPhpDevPackagesAction $installPhpDevPackages,
        private readonly InstallNodePackagesAction $installNodePackages,
        private readonly PublishConfigFilesAction $publishConfigFiles,
        private readonly PublishWorkflowsAction $publishWorkflows,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        intro('Akira Laravel Setup');

        if (! $this->packageDetector->hasComposerJson()) {
            warning('composer.json not found. Please run this command in a Laravel project.');

            return self::FAILURE;
        }

        $selection = $this->collectUserChoices();

        $this->executeSetup($selection);

        outro('✅ Setup completed successfully!');

        return self::SUCCESS;
    }

    private function executeSetup(PackageSelection $selection): void
    {
        $steps = $this->calculateSteps($selection);

        progress(
            label: 'Setting up your Laravel project...',
            steps: $steps,
            callback: function (string $step) use ($selection): void {
                match ($step) {
                    'php-require' => $this->installPhpRequire($selection),
                    'php-require-dev' => $this->installPhpRequireDev($selection),
                    'node-packages' => $this->installNodePackages($selection),
                    'composer-scripts' => $this->addComposerScripts(),
                    'package-json-scripts' => $this->addPackageJsonScripts(),
                    'config-files' => $this->publishConfigFiles(),
                    'workflows' => $this->publishWorkflows(),
                    default => null,
                };
            },
        );

        $this->displaySummary($selection);
    }
}
