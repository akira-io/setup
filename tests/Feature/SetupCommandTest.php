<?php

declare(strict_types=1);

it('registers setup command', function (): void {
    $this->artisan('list')
        ->assertSuccessful()
        ->expectsOutputToContain('akira:setup');
});

it('has correct command signature', function (): void {
    expect(
        $this->app->make(Akira\Setup\Console\SetupCommand::class))->toHaveProperty('signature', 'akira:setup');
});

it('has correct command description', function (): void {
    $command = $this->app->make(Akira\Setup\Console\SetupCommand::class);

    expect($command->getDescription())
        ->toBeString()
        ->toContain('Interactive Laravel project setup');
});
