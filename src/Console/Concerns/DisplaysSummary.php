<?php

declare(strict_types=1);

namespace Akira\Setup\Console\Concerns;

use Akira\Setup\DTOs\PackageSelection;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

trait DisplaysSummary
{
    private function displaySummary(PackageSelection $selection): void
    {
        $summary = [];

        if ($selection->phpRequire !== []) {
            $summary[] = '✓ PHP packages installed: '.count($selection->phpRequire);
        }

        if ($selection->phpRequireDev !== []) {
            $summary[] = '✓ PHP dev packages installed: '.count($selection->phpRequireDev);
        }

        if ($selection->nodeDevDependencies !== []) {
            $summary[] = '✓ Node.js dev dependencies installed: '.count($selection->nodeDevDependencies);
        }

        $summary[] = '✓ Composer scripts configured';
        $summary[] = '✓ Configuration files published (pint.json, rector.php, .release-it.json)';

        if ($selection->installWorkflows) {
            $summary[] = '✓ GitHub Actions workflows installed';
        }

        note(implode("\n", $summary));

        info('Next steps:');
        info('  → Run "composer test" to verify your setup');
        info('  → Run "composer lint" to format your code');
        info('  → Run "npm run release" to create a new release');
    }
}
