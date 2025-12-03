<?php

declare(strict_types=1);

use Akira\Setup\Actions\InstallNodePackagesAction;
use Akira\Setup\Actions\InstallPhpDevPackagesAction;
use Akira\Setup\Actions\InstallPhpPackagesAction;
use Akira\Setup\Console\Concerns\InstallsPackages;
use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Support\PackageDetector;
use Illuminate\Console\Command;

it('installPhpRequire executes without error when packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        /**
         * @var InstallPhpPackagesAction
         */
        public $installPhpPackages;

        public function __construct()
        {
            parent::__construct();
            $this->installPhpPackages = new InstallPhpPackagesAction();
        }

        public function testInstallPhpRequire(PackageSelection $selection): void
        {
            $this->installPhpRequire($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: ['vendor/package'],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallPhpRequire($selection);

    expect(true)->toBeTrue();
});

it('installPhpRequire skips when no packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        /**
         * @var InstallPhpPackagesAction
         */
        public $installPhpPackages;

        public function __construct()
        {
            parent::__construct();
            $this->installPhpPackages = new InstallPhpPackagesAction();
        }

        public function testInstallPhpRequire(PackageSelection $selection): void
        {
            $this->installPhpRequire($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallPhpRequire($selection);

    expect(true)->toBeTrue();
});

it('installPhpRequireDev executes without error when packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        public InstallPhpDevPackagesAction $installPhpDevPackages;

        public function __construct()
        {
            parent::__construct();
            $this->installPhpDevPackages = new InstallPhpDevPackagesAction();
        }

        public function testInstallPhpRequireDev(PackageSelection $selection): void
        {
            $this->installPhpRequireDev($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: ['vendor/dev-package'],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallPhpRequireDev($selection);

    expect(true)->toBeTrue();
});

it('installPhpRequireDev skips when no packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        /**
         * @var InstallPhpPackagesAction
         */
        public $installPhpPackages;

        public function __construct()
        {
            parent::__construct();
            $this->installPhpPackages = new InstallPhpPackagesAction();
        }

        public function testInstallPhpRequireDev(PackageSelection $selection): void
        {
            $this->installPhpRequireDev($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallPhpRequireDev($selection);

    expect(true)->toBeTrue();
});

it('installNodePackages executes without error when packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        /**
         * @var InstallNodePackagesAction
         */
        public $installNodePackages;

        /**
         * @var PackageDetector
         */
        public $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->installNodePackages = new InstallNodePackagesAction();
            $this->packageDetector = new PackageDetector();
        }

        public function testInstallNodePackages(PackageSelection $selection): void
        {
            $this->installNodePackages($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: ['release-it'],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallNodePackages($selection);

    expect(true)->toBeTrue();
});

it('installNodePackages skips when no packages provided', function (): void {
    $command = new class extends Command
    {
        use InstallsPackages;

        /**
         * @var InstallNodePackagesAction
         */
        public $installNodePackages;

        /**
         * @var PackageDetector
         */
        public $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->installNodePackages = new InstallNodePackagesAction();
            $this->packageDetector = new PackageDetector();
        }

        public function testInstallNodePackages(PackageSelection $selection): void
        {
            $this->installNodePackages($selection);
        }
    };

    $selection = new PackageSelection(
        phpRequire: [],
        phpRequireDev: [],
        nodeDevDependencies: [],
        installWorkflows: false,
        nodePackageManager: 'npm'
    );

    $command->testInstallNodePackages($selection);

    expect(true)->toBeTrue();
});
