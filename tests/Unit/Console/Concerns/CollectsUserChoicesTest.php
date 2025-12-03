<?php

declare(strict_types=1);

use Akira\Setup\Console\Concerns\CollectsUserChoices;
use Akira\Setup\DTOs\PackageSelection;
use Akira\Setup\Support\PackageDetector;
use Illuminate\Console\Command;

it('trait can be used in a command class', function (): void {
    $command = new class extends Command
    {
        use CollectsUserChoices;

        public PackageDetector $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->packageDetector = new PackageDetector();
        }
    };

    expect($command)->toBeInstanceOf(Command::class);
});

it('trait has collectUserChoices method', function (): void {
    $command = new class extends Command
    {
        use CollectsUserChoices;

        public PackageDetector $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->packageDetector = new PackageDetector();
        }
    };

    expect(method_exists($command, 'collectUserChoices'))->toBeTrue();
});

it('collectUserChoices method returns PackageSelection', function (): void {
    $reflection = new ReflectionMethod(new class extends Command
    {
        use CollectsUserChoices;

        public PackageDetector $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->packageDetector = new PackageDetector();
        }
    }, 'collectUserChoices');

    $returnType = $reflection->getReturnType();

    expect($returnType->getName())->toBe(PackageSelection::class);
});

it('collectUserChoices method is private', function (): void {
    $reflection = new ReflectionMethod(new class extends Command
    {
        use CollectsUserChoices;

        public PackageDetector $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->packageDetector = new PackageDetector();
        }
    }, 'collectUserChoices');

    expect($reflection->isPrivate())->toBeTrue();
});

it('trait requires packageDetector property', function (): void {
    $reflection = new ReflectionClass(new class extends Command
    {
        use CollectsUserChoices;

        public PackageDetector $packageDetector;

        public function __construct()
        {
            parent::__construct();
            $this->packageDetector = new PackageDetector();
        }
    });

    expect($reflection->hasProperty('packageDetector'))->toBeTrue();
});

it('collectUserChoices uses correct prompt functions', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('note(')
        ->and($source)->toContain('multiselect(')
        ->and($source)->toContain('select(')
        ->and($source)->toContain('confirm(');
});

it('collectUserChoices uses all enum classes', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('PhpPackage::toOptions()')
        ->and($source)->toContain('PhpPackage::allValues()')
        ->and($source)->toContain('PhpDevPackage::toOptions()')
        ->and($source)->toContain('PhpDevPackage::allValues()')
        ->and($source)->toContain('NodeDevPackage::toOptions()')
        ->and($source)->toContain('NodeDevPackage::allValues()');
});

it('collectUserChoices checks for package.json', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('hasPackageJson()');
});

it('collectUserChoices detects node package manager', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('detectNodePackageManager()');
});

it('collectUserChoices returns PackageSelection instance', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('return new PackageSelection(');
});

it('collectUserChoices initializes defaults correctly', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('$nodeDevDependencies = []')
        ->and($source)->toContain("nodePackageManager = 'npm'");
});

it('collectUserChoices provides correct package manager options', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain("'npm'")
        ->and($source)->toContain("'pnpm'")
        ->and($source)->toContain("'yarn'")
        ->and($source)->toContain("'bun'");
});

it('collectUserChoices has proper type annotations', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('@var array<string>')
        ->and($source)->toContain('$phpRequire')
        ->and($source)->toContain('$phpRequireDev')
        ->and($source)->toContain('$nodeDevDependencies');
});

it('collectUserChoices casts select result to string', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('(string) select(');
});

it('collectUserChoices sets confirm default to true', function (): void {
    $source = file_get_contents(__DIR__.'/../../../../src/Console/Concerns/CollectsUserChoices.php');

    expect($source)->toContain('default: true');
});
