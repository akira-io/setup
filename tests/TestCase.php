<?php
declare(strict_types=1);

namespace Akira\Setup\Tests;

use Akira\Debugger\DebuggerServiceProvider;
use Akira\Setup\AkiraSetupServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AkiraSetupServiceProvider::class,
            DebuggerServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        //
    }
}
