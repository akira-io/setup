<?php

declare(strict_types=1);

use Akira\Setup\Console\SetupCommand;

it('registers setup command', function () {
    $this->artisan('list')
        ->assertSuccessful()
        ->expectsOutputToContain('akira:setup');
});

it('has correct command signature', function () {
    $command = $this->app->make(SetupCommand::class);
    expect($command->getName())->toBe('akira:setup');
});

it('has correct command description', function () {
    $command = $this->app->make(SetupCommand::class);
    expect($command->getDescription())
        ->toBeString()
        ->toContain('Interactive Laravel project setup');
});

it('can be instantiated', function () {
    $command = $this->app->make(SetupCommand::class);
    expect($command)->toBeInstanceOf(SetupCommand::class);
});

it('has all required dependencies via reflection', function () {
    $command = $this->app->make(SetupCommand::class);
    $reflection = new ReflectionClass($command);

    expect($reflection->hasProperty('packageDetector'))->toBeTrue()
        ->and($reflection->hasProperty('fileManager'))->toBeTrue()
        ->and($reflection->hasProperty('installPhpPackages'))->toBeTrue()
        ->and($reflection->hasProperty('installNodePackages'))->toBeTrue()
        ->and($reflection->hasProperty('publishConfigFiles'))->toBeTrue()
        ->and($reflection->hasProperty('publishWorkflows'))->toBeTrue();
});

it('uses all required traits', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    $traits = $reflection->getTraitNames();

    expect($traits)->toContain('Akira\Setup\Console\Concerns\CalculatesSetupSteps')
        ->and($traits)->toContain('Akira\Setup\Console\Concerns\CollectsUserChoices')
        ->and($traits)->toContain('Akira\Setup\Console\Concerns\DisplaysSummary')
        ->and($traits)->toContain('Akira\Setup\Console\Concerns\InstallsPackages')
        ->and($traits)->toContain('Akira\Setup\Console\Concerns\PublishesAssets');
});

it('is a final class', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    expect($reflection->isFinal())->toBeTrue();
});

it('extends Laravel Command', function () {
    $command = $this->app->make(SetupCommand::class);
    expect($command)->toBeInstanceOf(\Illuminate\Console\Command::class);
});

it('has handle method', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    expect($reflection->hasMethod('handle'))->toBeTrue();
});

it('has executeSetup method', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    expect($reflection->hasMethod('executeSetup'))->toBeTrue();
});

it('handle method returns int', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    $method = $reflection->getMethod('handle');
    $returnType = $method->getReturnType();
    
    expect($returnType->getName())->toBe('int');
});

it('command has proper constructor signature', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    $constructor = $reflection->getConstructor();
    $parameters = $constructor->getParameters();
    
    expect(count($parameters))->toBe(6);
});

it('all constructor parameters are readonly', function () {
    $reflection = new ReflectionClass(SetupCommand::class);
    $properties = $reflection->getProperties();
    
    $readonlyCount = 0;
    foreach ($properties as $property) {
        if ($property->isReadOnly()) {
            $readonlyCount++;
        }
    }
    
    expect($readonlyCount)->toBeGreaterThanOrEqual(6);
});
