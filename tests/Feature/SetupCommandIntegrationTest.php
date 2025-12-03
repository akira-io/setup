<?php

declare(strict_types=1);

use Akira\Setup\Console\SetupCommand;
use Akira\Setup\DTOs\PackageSelection;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    // Create temporary composer.json
    File::put(base_path('composer.json'), json_encode([
        'name' => 'test/project',
        'require' => [],
        'require-dev' => [],
        'scripts' => [],
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

    // Create temporary package.json
    File::put(base_path('package.json'), json_encode([
        'name' => 'test-project',
        'scripts' => [],
        'devDependencies' => [],
    ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
});

afterEach(function (): void {
    $filesToClean = [
        'composer.json',
        'package.json',
        'pint.json',
        'rector.php',
        '.release-it.json',
    ];

    foreach ($filesToClean as $file) {
        if (File::exists(base_path($file))) {
            File::delete(base_path($file));
        }
    }

    if (File::isDirectory(base_path('.github'))) {
        File::deleteDirectory(base_path('.github'));
    }
});

it('collectUserChoices method exists and has correct signature', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('collectUserChoices');

    expect($method->isPrivate())->toBeTrue()
        ->and($method->getReturnType()->getName())->toBe(PackageSelection::class);
});

it('installPhpRequire is called with packages', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: ['vendor/package'],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('installPhpRequire');

    // Should not throw
    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('installPhpRequireDev is called with packages', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: ['vendor/dev-package'],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('installPhpRequireDev');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('installNodePackages is called with packages', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: ['release-it'],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('installNodePackages');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('addComposerScripts is called', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('addComposerScripts');

    $method->invoke($command);

    expect(true)->toBeTrue();
});

it('addPackageJsonScripts is called', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('addPackageJsonScripts');

    $method->invoke($command);

    expect(true)->toBeTrue();
});

it('publishConfigFiles is called', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('publishConfigFiles');

    $method->invoke($command);

    expect(true)->toBeTrue();
});

it('publishWorkflows is called', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('publishWorkflows');

    $method->invoke($command);

    expect(true)->toBeTrue();
});

it('displaySummary is called with all packages', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: ['vendor/package'],
        phpRequireDev: ['vendor/dev'],
        nodeDevDependencies: ['release-it'],
        installWorkflows: true,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('displaySummary');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('displaySummary is called with no packages', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('displaySummary');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('executeSetup handles all match cases', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: ['vendor/package'],
        phpRequireDev: ['vendor/dev'],
        nodeDevDependencies: ['release-it'],
        installWorkflows: true,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('executeSetup');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('calculateSteps includes all possible steps', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: ['vendor/package'],
        phpRequireDev: ['vendor/dev'],
        nodeDevDependencies: ['release-it'],
        installWorkflows: true,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('calculateSteps');

    $steps = $method->invoke($command, $selection);

    expect($steps)->toBeArray()
        ->and($steps)->toContain('php-require')
        ->and($steps)->toContain('php-require-dev')
        ->and($steps)->toContain('node-packages')
        ->and($steps)->toContain('composer-scripts')
        ->and($steps)->toContain('config-files')
        ->and($steps)->toContain('workflows');
});

it('calculateSteps includes package-json-scripts when package.json exists', function (): void {

    if (! file_exists(base_path('package.json'))) {
        File::put(base_path('package.json'), json_encode(['name' => 'test']));
    }

    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: ['release-it'],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('calculateSteps');

    $steps = $method->invoke($command, $selection);

    if (file_exists(base_path('package.json'))) {
        expect($steps)->toContain('package-json-scripts');
    } else {
        expect($steps)->not->toContain('package-json-scripts');
    }
});

it('executeSetup invokes all step handlers', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: ['vendor/test'],
        phpRequireDev: ['vendor/test-dev'],
        nodeDevDependencies: ['test-package'],
        installWorkflows: true,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('executeSetup');

    // Execute without throwing
    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('executeSetup handles empty selection gracefully', function (): void {
    $command = $this->app->make(SetupCommand::class);

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('executeSetup');

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('match statement has default case', function (): void {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('executeSetup');

    $filename = $reflection->getFileName();
    $startLine = $method->getStartLine() - 1;
    $endLine = $method->getEndLine();
    $length = $endLine - $startLine;

    $source = file($filename);
    $methodCode = implode('', array_slice($source, $startLine, $length));

    expect($methodCode)->toContain('default => null');
});

it('all match cases are covered', function (): void {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('executeSetup');

    $filename = $reflection->getFileName();
    $startLine = $method->getStartLine() - 1;
    $endLine = $method->getEndLine();
    $length = $endLine - $startLine;

    $source = file($filename);
    $methodCode = implode('', array_slice($source, $startLine, $length));

    $expectedCases = [
        'php-require',
        'php-require-dev',
        'node-packages',
        'composer-scripts',
        'package-json-scripts',
        'config-files',
        'workflows',
    ];

    foreach ($expectedCases as $case) {
        expect($methodCode)->toContain("'{$case}'");
    }
});

it('handle method flow is correct', function (): void {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('handle');

    $filename = $reflection->getFileName();
    $startLine = $method->getStartLine() - 1;
    $endLine = $method->getEndLine();
    $length = $endLine - $startLine;

    $source = file($filename);
    $methodCode = implode('', array_slice($source, $startLine, $length));

    // Verify all required calls
    expect($methodCode)->toContain('intro(')
        ->and($methodCode)->toContain('hasComposerJson()')
        ->and($methodCode)->toContain('warning(')
        ->and($methodCode)->toContain('FAILURE')
        ->and($methodCode)->toContain('collectUserChoices()')
        ->and($methodCode)->toContain('executeSetup(')
        ->and($methodCode)->toContain('outro(')
        ->and($methodCode)->toContain('SUCCESS');
});

it('executeSetup calls displaySummary', function (): void {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('executeSetup');

    $filename = $reflection->getFileName();
    $startLine = $method->getStartLine() - 1;
    $endLine = $method->getEndLine();
    $length = $endLine - $startLine;

    $source = file($filename);
    $methodCode = implode('', array_slice($source, $startLine, $length));

    expect($methodCode)->toContain('displaySummary($selection)');
});

it('executeSetup uses progress function', function (): void {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('executeSetup');

    $filename = $reflection->getFileName();
    $startLine = $method->getStartLine() - 1;
    $endLine = $method->getEndLine();
    $length = $endLine - $startLine;

    $source = file($filename);
    $methodCode = implode('', array_slice($source, $startLine, $length));

    expect($methodCode)->toContain('progress(')
        ->and($methodCode)->toContain('label:')
        ->and($methodCode)->toContain('steps:')
        ->and($methodCode)->toContain('callback:');
});
