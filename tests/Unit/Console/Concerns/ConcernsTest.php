<?php

declare(strict_types=1);

use Akira\Setup\Actions\InstallNodePackagesAction;
use Akira\Setup\Actions\InstallPhpPackagesAction;
use Akira\Setup\Actions\PublishConfigFilesAction;
use Akira\Setup\Actions\PublishWorkflowsAction;
use Akira\Setup\Console\Concerns\CalculatesSetupSteps;
use Akira\Setup\Console\Concerns\DisplaysSummary;
use Akira\Setup\Console\Concerns\InstallsPackages;
use Akira\Setup\Console\Concerns\PublishesAssets;
use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Support\FileManager;
use Akira\Setup\Support\PackageDetector;

describe('CalculatesSetupSteps', function () {
    it('calculates all steps when everything is selected', function () {
        $trait = new class {
            use CalculatesSetupSteps;
            
            public $packageDetector;
            
            public function __construct() {
                $this->packageDetector = new PackageDetector();
            }
            
            public function testCalculate(PackageSelection $selection): array {
                return $this->calculateSteps($selection);
            }
        };
        
        $selection = new PackageSelection(
            phpRequire: ['pkg1'],
            phpRequireDev: ['pkg2'],
            nodeDevDependencies: ['pkg3'],
            installWorkflows: true,
            nodePackageManager: 'npm'
        );
        
        $steps = $trait->testCalculate($selection);
        
        expect($steps)->toBeArray()
            ->and($steps)->toContain('php-require')
            ->and($steps)->toContain('php-require-dev')
            ->and($steps)->toContain('node-packages')
            ->and($steps)->toContain('composer-scripts')
            ->and($steps)->toContain('config-files')
            ->and($steps)->toContain('workflows');
    });
    
    it('skips empty steps', function () {
        $trait = new class {
            use CalculatesSetupSteps;
            
            public $packageDetector;
            
            public function __construct() {
                $this->packageDetector = new PackageDetector();
            }
            
            public function testCalculate(PackageSelection $selection): array {
                return $this->calculateSteps($selection);
            }
        };
        
        $selection = new PackageSelection(
            phpRequire: [],
            phpRequireDev: [],
            nodeDevDependencies: [],
            installWorkflows: false,
            nodePackageManager: 'npm'
        );
        
        $steps = $trait->testCalculate($selection);
        
        expect($steps)->not->toContain('php-require')
            ->and($steps)->not->toContain('php-require-dev')
            ->and($steps)->not->toContain('node-packages')
            ->and($steps)->not->toContain('workflows')
            ->and($steps)->toContain('composer-scripts')
            ->and($steps)->toContain('config-files');
    });
});

describe('InstallsPackages', function () {
    it('has installPhpRequire method', function () {
        $trait = new class {
            use InstallsPackages;
            
            public $installPhpPackages;
            
            public function __construct() {
                $this->installPhpPackages = new InstallPhpPackagesAction();
            }
            
            public function testInstallPhpRequire(PackageSelection $selection): void {
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
        
        $trait->testInstallPhpRequire($selection);
        expect(true)->toBeTrue();
    });
    
    it('has installPhpRequireDev method', function () {
        $trait = new class {
            use InstallsPackages;
            
            public $installPhpPackages;
            
            public function __construct() {
                $this->installPhpPackages = new InstallPhpPackagesAction();
            }
            
            public function testInstallPhpRequireDev(PackageSelection $selection): void {
                $this->installPhpRequireDev($selection);
            }
        };
        
        $selection = new PackageSelection(
            phpRequire: [],
            phpRequireDev: ['vendor/dev'],
            nodeDevDependencies: [],
            installWorkflows: false,
            nodePackageManager: 'npm'
        );
        
        $trait->testInstallPhpRequireDev($selection);
        expect(true)->toBeTrue();
    });
    
    it('has installNodePackages method', function () {
        $trait = new class {
            use InstallsPackages;
            
            public $installNodePackages;
            public $packageDetector;
            
            public function __construct() {
                $this->installNodePackages = new InstallNodePackagesAction();
                $this->packageDetector = new PackageDetector();
            }
            
            public function testInstallNodePackages(PackageSelection $selection): void {
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
        
        $trait->testInstallNodePackages($selection);
        expect(true)->toBeTrue();
    });
});

describe('PublishesAssets', function () {
    it('has addComposerScripts method', function () {
        $trait = new class {
            use PublishesAssets;
            
            public $fileManager;
            
            public function __construct() {
                $this->fileManager = new FileManager();
            }
            
            public function testAddComposerScripts(): void {
                $this->addComposerScripts();
            }
        };
        
        $trait->testAddComposerScripts();
        expect(true)->toBeTrue();
    });
    
    it('has addPackageJsonScripts method', function () {
        $trait = new class {
            use PublishesAssets;
            
            public $fileManager;
            
            public function __construct() {
                $this->fileManager = new FileManager();
            }
            
            public function testAddPackageJsonScripts(): void {
                $this->addPackageJsonScripts();
            }
        };
        
        $trait->testAddPackageJsonScripts();
        expect(true)->toBeTrue();
    });
    
    it('has publishConfigFiles method', function () {
        $trait = new class {
            use PublishesAssets;
            
            public $publishConfigFiles;
            
            public function __construct() {
                $this->publishConfigFiles = new PublishConfigFilesAction();
            }
            
            public function testPublishConfigFiles(): void {
                $this->publishConfigFiles();
            }
        };
        
        $trait->testPublishConfigFiles();
        expect(true)->toBeTrue();
    });
    
    it('has publishWorkflows method', function () {
        $trait = new class {
            use PublishesAssets;
            
            public $publishWorkflows;
            
            public function __construct() {
                $this->publishWorkflows = new PublishWorkflowsAction();
            }
            
            public function testPublishWorkflows(): void {
                $this->publishWorkflows();
            }
        };
        
        $trait->testPublishWorkflows();
        expect(true)->toBeTrue();
    });
});

describe('DisplaysSummary', function () {
    it('has displaySummary method', function () {
        $trait = new class {
            use DisplaysSummary;
            
            public function testDisplaySummary(PackageSelection $selection): void {
                // Don't actually display, just check method exists
                expect(method_exists($this, 'displaySummary'))->toBeTrue();
            }
        };
        
        $selection = new PackageSelection([], [], [], false, 'npm');
        $trait->testDisplaySummary($selection);
    });
});
