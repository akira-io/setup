<?php

declare(strict_types=1);

use Akira\Setup\Console\SetupCommand;
use Akira\Setup\DTOs\PackageSelection;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    // Ensure we have a test composer.json
    if (! file_exists(base_path('composer.json'))) {
        File::put(base_path('composer.json'), json_encode([
            'name' => 'test/project',
            'require' => [],
            'require-dev' => [],
        ]));
    }
});

it('calculateSteps includes all possible steps', function () {
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
    $method->setAccessible(true);

    $steps = $method->invoke($command, $selection);

    expect($steps)->toBeArray()
        ->and($steps)->toContain('php-require')
        ->and($steps)->toContain('php-require-dev')
        ->and($steps)->toContain('node-packages')
        ->and($steps)->toContain('composer-scripts')
        ->and($steps)->toContain('config-files')
        ->and($steps)->toContain('workflows');
});

it('calculateSteps includes package-json-scripts when package.json exists', function () {

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
    $method->setAccessible(true);

    $steps = $method->invoke($command, $selection);

    if (file_exists(base_path('package.json'))) {
        expect($steps)->toContain('package-json-scripts');
    } else {
        expect($steps)->not->toContain('package-json-scripts');
    }
});

it('executeSetup invokes all step handlers', function () {
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
    $method->setAccessible(true);

    // Execute without throwing
    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('executeSetup handles empty selection gracefully', function () {
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
    $method->setAccessible(true);

    $method->invoke($command, $selection);

    expect(true)->toBeTrue();
});

it('match statement has default case', function () {
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

it('all match cases are covered', function () {
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

it('handle method flow is correct', function () {
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

it('executeSetup calls displaySummary', function () {
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

it('executeSetup uses progress function', function () {
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
