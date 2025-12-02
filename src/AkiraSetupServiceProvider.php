<?php

declare(strict_types=1);

namespace Akira\Setup;

use Akira\Setup\Console\SetupCommand;
use Illuminate\Support\ServiceProvider;

final class AkiraSetupServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SetupCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        //
    }
}
