# Architecture Overview

This document explains the high-level architecture, design patterns, and project structure of Akira Setup.

## Design Philosophy

Akira Setup follows **Spatie-level code quality standards** and modern Laravel best practices:

- **Type Safety** - Strict types, full type hints, PHPStan compliance
- **Single Responsibility** - Each class has one clear purpose
- **Composition Over Inheritance** - Traits for behavior composition
- **Dependency Injection** - No facades or service locators
- **Immutability** - Final classes, readonly properties
- **Action Pattern** - Business logic in dedicated action classes
- **No Side Effects** - Pure, testable functions

## Project Structure

```
src/
├── AkiraSetupServiceProvider.php          # Service provider registration
├── Console/
│   ├── SetupCommand.php                   # Main command orchestrator
│   └── Concerns/                          # Trait composition
│       ├── CalculatesSetupSteps.php       # Step determination
│       ├── CollectsUserChoices.php        # User prompts
│       ├── DisplaysSummary.php            # Summary display
│       ├── InstallsPackages.php           # Package delegation
│       └── PublishesAssets.php            # Asset publishing
├── Actions/                               # Business logic
│   ├── InstallPhpPackagesAction.php       # Composer installation
│   ├── InstallNodePackagesAction.php      # Node package installation
│   ├── PublishConfigFilesAction.php       # Config file publishing
│   └── PublishWorkflowsAction.php         # Workflow publishing
├── DTOs/                                  # Data transfer objects
│   ├── PackageSelection.php               # Selected packages
│   └── ComposerScripts.php                # Composer scripts definition
├── Enums/                                 # Package definitions
│   ├── PhpPackage.php                     # Production packages
│   ├── PhpDevPackage.php                  # Dev packages
│   └── NodeDevPackage.php                 # Node packages
└── Support/                               # Utilities
    ├── FileManager.php                    # File operations
    └── PackageDetector.php                # System detection

stubs/
├── pint.json                              # Code formatting config
├── rector.php                             # Refactoring config
├── release-it.json                        # Release config
└── github/                                # GitHub templates
    ├── workflows/
    ├── issue_template/
    └── config files
```

## Core Components

### SetupCommand

The main entry point and orchestrator:

```php
class SetupCommand extends Command
{
    // Uses five traits for composition
    use CalculatesSetupSteps;
    use CollectsUserChoices;
    use InstallsPackages;
    use PublishesAssets;
    use DisplaysSummary;

    public function handle(): int
    {
        // 1. Validate composer.json exists
        // 2. Collect user choices
        // 3. Execute setup steps
        // 4. Display summary
    }
}
```

**Injected Dependencies:**
- `PackageDetector` - System capability detection
- `FileManager` - File operations
- `InstallPhpPackagesAction` - PHP installation
- `InstallNodePackagesAction` - Node installation
- `PublishConfigFilesAction` - Config publishing
- `PublishWorkflowsAction` - Workflow publishing

### Traits (Concerns)

Traits separate the command into focused concerns:

**CalculatesSetupSteps**
- Determines which steps to run based on user selections
- Returns array of step names: `php-require`, `php-require-dev`, `node-packages`, etc.

**CollectsUserChoices**
- Prompts for PHP production packages
- Prompts for PHP dev packages
- Prompts for Node packages (if package.json exists)
- Detects and prompts for Node package manager
- Confirms GitHub workflow installation
- Returns `PackageSelection` DTO

**InstallsPackages**
- Delegates to action classes
- Handles package installation execution
- Shows progress and status messages

**PublishesAssets**
- Adds Composer scripts
- Adds package.json scripts
- Publishes config files
- Publishes GitHub workflows

**DisplaysSummary**
- Shows installation summary
- Lists completed steps
- Provides next recommended commands

### Data Transfer Objects (DTOs)

**PackageSelection**
```php
readonly class PackageSelection {
    public array $phpRequire;              // Production packages
    public array $phpRequireDev;           // Dev packages
    public array $nodeDevDependencies;     // Node packages
    public bool $installWorkflows;         // GitHub workflows flag
    public string $nodePackageManager;     // npm|pnpm|yarn|bun
}
```

Encapsulates all user selections in a single, immutable object.

**ComposerScripts**
```php
readonly class ComposerScripts {
    static public function getScripts(): array {
        return [
            'test' => [...],
            'lint' => [...],
            // ... all composer scripts
        ];
    }
}
```

Defines all composer scripts that get added to `composer.json`.

### Actions

Business logic is encapsulated in action classes:

**InstallPhpPackagesAction**
- Executes `composer require` for production packages
- Executes `composer require --dev` for dev packages
- Uses Symfony Process with 600s timeout
- Returns success boolean

**InstallNodePackagesAction**
- Executes package manager command (npm/pnpm/yarn/bun)
- Auto-detects package manager
- Uses Symfony Process
- Returns success boolean

**PublishConfigFilesAction**
- Copies `pint.json` to project root
- Copies `rector.php` to project root
- Copies `.release-it.json` to project root
- Creates/overwrites files

**PublishWorkflowsAction**
- Publishes GitHub Actions workflows
- Publishes GitHub issue templates
- Publishes GitHub configuration files
- Ensures `.github/` directory exists

### Support Classes

**PackageDetector**
```php
class PackageDetector {
    public function detectNodePackageManager(): string
    public function getInstallCommand(string $manager): string
    public function hasComposerJson(): bool
    public function hasPackageJson(): bool
}
```

Uses `ExecutableFinder` to detect installed package managers in priority order: bun, pnpm, yarn, npm.

**FileManager**
```php
class FileManager {
    public function copyStub(string $stub, string $destination): void
    public function updateJson(string $path, array $data): void
    public function mergeComposerScripts(array $scripts): void
    public function addPackageJsonScripts(array $scripts): void
}
```

Wraps Laravel's Filesystem for project-specific operations. Features:
- JSON file merging
- Script array handling
- Pretty-printed JSON output
- Automatic directory creation

### Enums

Type-safe package definitions:

**PhpPackage** - 2 production packages
**PhpDevPackage** - 14 development packages
**NodeDevPackage** - 4 Node packages

Each enum provides:
- `toOptions()` - UI label mapping
- `allValues()` - All enum values
- `label()` - Human-readable label

## Workflow

The complete setup process:

```
User runs: php artisan akira:setup
              ↓
SetupCommand validates composer.json
              ↓
CollectsUserChoices prompts for selections
              ↓
PackageSelection DTO created
              ↓
CalculatesSetupSteps determines which to run
              ↓
For each step:
  ├─ InstallPhpPackagesAction (if needed)
  ├─ InstallNodePackagesAction (if needed)
  ├─ FileManager::mergeComposerScripts
  ├─ FileManager::addPackageJsonScripts
  ├─ PublishConfigFilesAction
  └─ PublishWorkflowsAction
              ↓
DisplaysSummary shows results
              ↓
Exit with success
```

## Design Patterns Used

### 1. Action Pattern

All business logic is in action classes with a `handle()` method:

```php
final readonly class PublishConfigFilesAction {
    public function __construct(private FileManager $fileManager) {}

    public function handle(PackageSelection $selection): void {
        // Business logic here
    }
}
```

Benefits:
- Testable without framework
- Reusable across commands/controllers
- Clear intent and responsibility
- Easy to inject dependencies

### 2. Data Transfer Objects (DTOs)

Immutable objects represent data structures:

```php
readonly class PackageSelection {
    public function __construct(
        public array $phpRequire,
        public array $phpRequireDev,
        // ...
    ) {}
}
```

Benefits:
- Type-safe parameter passing
- Immutable data
- Self-documenting
- IDE autocompletion

### 3. Trait Composition

Traits break down command complexity:

```php
class SetupCommand extends Command {
    use CalculatesSetupSteps;
    use CollectsUserChoices;
    use InstallsPackages;
    use PublishesAssets;
    use DisplaysSummary;
}
```

Benefits:
- Readable trait names describe functionality
- Each trait is independently testable
- Easier to navigate code
- Clear separation of concerns

### 4. Strategy Pattern (Enums)

Enums define different strategies:

```php
enum PhpDevPackage: string {
    case PEST = 'pestphp/pest';
    case RECTOR = 'rector/rector';

    public function label(): string { ... }
}
```

Benefits:
- Type-safe package definitions
- Prevents invalid values
- Self-documenting
- Easy to add/remove packages

### 5. Factory Pattern

Actions are created and injected via dependency injection, acting as factories.

## Code Standards

### Type Safety

- `declare(strict_types=1)` on all files
- Full return types on all methods
- Constructor property promotion
- No `mixed` or `any` types

```php
final readonly class Example {
    public function __construct(
        private FileManager $fileManager,
        private string $path,
    ) {}

    public function handle(PackageSelection $selection): void {
        // Full types everywhere
    }
}
```

### PHPStan Compliance

- Level max PHPStan configuration
- All properties have types
- Docblocks for static analysis
- No dynamic properties

### PSR-12 Compliance

- PSR-12 code formatting
- 4-space indentation
- Proper spacing
- Consistent naming

### No Debugging Functions

Architecture tests prevent:
- `dd()`, `dump()`
- `ray()`, `ad()`
- `var_dump()`

Ensures code is clean and production-ready.

## Dependencies

### Runtime

- `illuminate/console` - Laravel commands
- `illuminate/support` - Laravel utilities
- `symfony/process` - Process execution
- `symfony/finder` - File finding

### Development

- `pestphp/pest` - Testing framework
- `phpstan/larastan` - Static analysis
- `rectorphp/rector` - Code refactoring
- `laravel/pint` - Code styling

## Service Provider

The `AkiraSetupServiceProvider` registers the command:

```php
class AkiraSetupServiceProvider extends ServiceProvider {
    public function register(): void {
        // Register command only in console
    }

    public function boot(): void {
        // Command is auto-registered
    }
}
```

Uses Laravel's auto-discovery mechanism, no manual registration needed.

## Extensibility

The architecture allows easy extension:

1. **Add New Packages** - Update enums in `Enums/`
2. **Add New Actions** - Create new action class with `handle()` method
3. **Add New Traits** - Create trait, add to `SetupCommand`
4. **Customize Configuration** - Update stub files in `stubs/`

All changes propagate automatically through dependency injection.

---

**← Previous:** [01 - Getting Started](./01-getting-started.md) | **Next:** [03 - Components Guide →](./03-components-guide.md)
