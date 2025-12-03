# Components Guide

Detailed documentation for each major component in Akira Setup.

## Table of Contents

1. [SetupCommand](#setupcommand)
2. [Traits (Concerns)](#traits-concerns)
3. [Actions](#actions)
4. [DTOs](#dtos)
5. [Enums](#enums)
6. [Support Classes](#support-classes)

## SetupCommand

**Location:** `src/Console/SetupCommand.php`

The main orchestrator that coordinates the entire setup process.

### Class Definition

```php
final class SetupCommand extends Command
{
    use CalculatesSetupSteps;
    use CollectsUserChoices;
    use InstallsPackages;
    use PublishesAssets;
    use DisplaysSummary;

    protected $signature = 'akira:setup';
    protected $description = 'Set up your Laravel project with industry best practices';
}
```

### Constructor Dependencies

```php
public function __construct(
    private PackageDetector $packageDetector,
    private FileManager $fileManager,
    private InstallPhpPackagesAction $installPhpPackagesAction,
    private InstallNodePackagesAction $installNodePackagesAction,
    private PublishConfigFilesAction $publishConfigFilesAction,
    private PublishWorkflowsAction $publishWorkflowsAction,
)
```

### Main Method: `handle()`

```php
public function handle(): int
{
    // 1. Validate composer.json exists
    if (! $this->fileManager->exists('composer.json')) {
        $this->error('composer.json not found');
        return self::FAILURE;
    }

    // 2. Collect user choices
    $selection = $this->collectUserChoices();

    // 3. Execute setup steps
    $this->executeSetup($selection);

    // 4. Display summary
    $this->displaySummary($selection);

    return self::SUCCESS;
}
```

### Key Methods

**validate()**
- Checks for `composer.json` file
- Validates it's a Laravel project
- Returns early with error if invalid

**collectUserChoices(): PackageSelection**
- Prompts for PHP production packages
- Prompts for PHP dev packages
- Prompts for Node packages (if applicable)
- Detects and prompts for Node package manager
- Confirms GitHub workflow installation
- Returns `PackageSelection` DTO

**executeSetup(PackageSelection $selection): void**
- Gets setup steps via `CalculatesSetupSteps` trait
- Shows progress bar with steps
- Executes each step in order
- Handles errors gracefully

**Process Flow**

```
Command starts
    ↓
Validate composer.json exists
    ↓
Collect user choices (via trait)
    ↓
Create PackageSelection DTO
    ↓
Calculate which steps to run (via trait)
    ↓
Show progress bar
    ↓
Execute each step:
  - Install PHP packages
  - Install Node packages
  - Merge composer scripts
  - Add npm scripts
  - Publish config files
  - Publish workflows
    ↓
Display summary (via trait)
    ↓
Exit with success
```

## Traits (Concerns)

### CalculatesSetupSteps

**Location:** `src/Console/Concerns/CalculatesSetupSteps.php`

Determines which setup steps to execute based on user selections.

```php
trait CalculatesSetupSteps
{
    protected function setupSteps(PackageSelection $selection): array
    {
        $steps = [];

        if (! empty($selection->phpRequire)) {
            $steps[] = 'php-require';
        }

        if (! empty($selection->phpRequireDev)) {
            $steps[] = 'php-require-dev';
        }

        if (! empty($selection->nodeDevDependencies)) {
            $steps[] = 'node-packages';
        }

        $steps[] = 'composer-scripts';
        $steps[] = 'package-json-scripts';

        if ($selection->installWorkflows) {
            $steps[] = 'config-files';
            $steps[] = 'workflows';
        }

        return $steps;
    }
}
```

**Returns:** Array of step names in execution order

**Conditional Steps:**
- `php-require` - Only if production packages selected
- `php-require-dev` - Only if dev packages selected
- `node-packages` - Only if Node packages selected
- `composer-scripts` - Always runs
- `package-json-scripts` - Always runs
- `config-files` - Only if workflows confirmed
- `workflows` - Only if workflows confirmed

### CollectsUserChoices

**Location:** `src/Console/Concerns/CollectsUserChoices.php`

Prompts the user for setup options via interactive prompts.

```php
trait CollectsUserChoices
{
    protected function collectUserChoices(): PackageSelection
    {
        $phpRequire = $this->promptForPhpPackages();
        $phpRequireDev = $this->promptForPhpDevPackages();
        $nodeManager = $this->detectNodePackageManager();
        $nodePackages = $this->promptForNodePackages();
        $workflows = $this->promptForWorkflows();

        return new PackageSelection(
            phpRequire: $phpRequire,
            phpRequireDev: $phpRequireDev,
            nodeDevDependencies: $nodePackages,
            installWorkflows: $workflows,
            nodePackageManager: $nodeManager,
        );
    }
}
```

**Prompts:**

1. **promptForPhpPackages()**
   - Multiselect from `PhpPackage` enum
   - Default: All packages selected
   - Returns array of package names

2. **promptForPhpDevPackages()**
   - Multiselect from `PhpDevPackage` enum
   - Default: All packages selected
   - Returns array of package names

3. **detectNodePackageManager()**
   - Auto-detects installed manager (bun, pnpm, yarn, npm)
   - Asks user to confirm or choose different
   - Returns manager name

4. **promptForNodePackages()**
   - Only if `package.json` exists
   - Multiselect from `NodeDevPackage` enum
   - Default: All packages selected
   - Returns array of package names

5. **promptForWorkflows()**
   - Confirms GitHub Actions installation
   - Default: Yes
   - Returns boolean

### InstallsPackages

**Location:** `src/Console/Concerns/InstallsPackages.php`

Handles delegation to package installation actions.

```php
trait InstallsPackages
{
    protected function executeStep(string $step, PackageSelection $selection): void
    {
        match ($step) {
            'php-require' => $this->installPhpPackagesAction->handle(
                $selection->phpRequire,
                isDev: false
            ),
            'php-require-dev' => $this->installPhpPackagesAction->handle(
                $selection->phpRequireDev,
                isDev: true
            ),
            'node-packages' => $this->installNodePackagesAction->handle(
                $selection->nodeDevDependencies,
                $selection->nodePackageManager
            ),
            // ... other steps
        };
    }
}
```

**Responsibilities:**
- Delegates to action classes
- Handles progress display
- Catches and displays errors
- Shows status messages

### PublishesAssets

**Location:** `src/Console/Concerns/PublishesAssets.php`

Handles publishing configuration files and GitHub workflows.

```php
trait PublishesAssets
{
    protected function publishAssets(PackageSelection $selection): void
    {
        $this->publishComposerScripts();
        $this->publishPackageJsonScripts($selection);
        $this->publishConfigFiles($selection);
        $this->publishWorkflows($selection);
    }
}
```

**Methods:**
- `publishComposerScripts()` - Adds test, lint, etc. scripts to composer.json
- `publishPackageJsonScripts()` - Adds release script to package.json
- `publishConfigFiles()` - Publishes pint.json, rector.php, .release-it.json
- `publishWorkflows()` - Publishes GitHub Actions workflows

### DisplaysSummary

**Location:** `src/Console/Concerns/DisplaysSummary.php`

Displays completion summary and next steps.

```php
trait DisplaysSummary
{
    protected function displaySummary(PackageSelection $selection): void
    {
        $this->newLine();
        $this->info('Setup completed successfully!');

        $this->line('Installed packages:');
        $this->line('  PHP: ' . count($selection->phpRequire));
        $this->line('  PHP Dev: ' . count($selection->phpRequireDev));
        $this->line('  Node: ' . count($selection->nodeDevDependencies));

        $this->line('Next steps:');
        $this->line('  composer test');
        $this->line('  composer lint');
    }
}
```

**Displays:**
- Success message
- Package counts
- Installed steps
- Recommended next commands

## Actions

All actions follow the Action Pattern with a single public `handle()` method.

### InstallPhpPackagesAction

**Location:** `src/Actions/InstallPhpPackagesAction.php`

Installs PHP packages via Composer.

```php
final readonly class InstallPhpPackagesAction
{
    public function __construct(private Process $process) {}

    public function handle(array $packages, bool $isDev = false): bool
    {
        $command = $isDev ? 'composer require --dev' : 'composer require';
        $command .= ' ' . implode(' ', $packages);

        return $this->process->call($command)->isSuccessful();
    }
}
```

**Parameters:**
- `$packages` (array) - Package names to install
- `$isDev` (bool) - Install as dev dependency

**Returns:** Boolean indicating success

**Process Details:**
- Timeout: 600 seconds
- Working directory: Project root
- Runs with `composer.json` auto-discovery

### InstallNodePackagesAction

**Location:** `src/Actions/InstallNodePackagesAction.php`

Installs Node packages via npm, pnpm, yarn, or bun.

```php
final readonly class InstallNodePackagesAction
{
    public function __construct(
        private Process $process,
        private PackageDetector $detector,
    ) {}

    public function handle(array $packages, string $manager = 'npm'): bool
    {
        $command = $this->detector->getInstallCommand($manager);
        $command .= ' ' . implode(' ', $packages);

        return $this->process->call($command)->isSuccessful();
    }
}
```

**Parameters:**
- `$packages` (array) - Package names to install
- `$manager` (string) - npm, pnpm, yarn, or bun

**Returns:** Boolean indicating success

**Supported Managers:**
- `npm install -D`
- `pnpm add -D`
- `yarn add -D`
- `bun add -d`

### PublishConfigFilesAction

**Location:** `src/Actions/PublishConfigFilesAction.php`

Publishes configuration file stubs to project root.

```php
final readonly class PublishConfigFilesAction
{
    public function __construct(private FileManager $fileManager) {}

    public function handle(PackageSelection $selection): void
    {
        $this->fileManager->copyStub('pint.json', 'pint.json');
        $this->fileManager->copyStub('rector.php', 'rector.php');
        $this->fileManager->copyStub('release-it.json', '.release-it.json');
    }
}
```

**Files Published:**
1. **pint.json** - Code formatting configuration (140+ rules)
2. **rector.php** - PHP refactoring rules (50+ rules)
3. **.release-it.json** - Release management configuration

**Overwrites:** Yes, existing files are replaced

### PublishWorkflowsAction

**Location:** `src/Actions/PublishWorkflowsAction.php`

Publishes GitHub Actions workflows and templates.

```php
final readonly class PublishWorkflowsAction
{
    public function __construct(private FileManager $fileManager) {}

    public function handle(PackageSelection $selection): void
    {
        $this->publishWorkflows();
        $this->publishIssueTemplates();
        $this->publishConfigFiles();
    }
}
```

**Workflows Published:**
- `.github/workflows/tests.yml` - CI/CD testing
- `.github/workflows/release-discord.yml` - Release notifications

**Issue Templates:**
- `.github/issue_template/bug.yml` - Bug reports
- `.github/issue_template/feature.yml` - Feature requests

**Config Files:**
- `.github/dependabot.yml` - Dependency updates
- `.github/FUNDING.yml` - GitHub Sponsors

## DTOs

### PackageSelection

**Location:** `src/DTOs/PackageSelection.php`

Immutable data transfer object containing user's setup selections.

```php
readonly class PackageSelection
{
    public function __construct(
        public array $phpRequire,
        public array $phpRequireDev,
        public array $nodeDevDependencies,
        public bool $installWorkflows,
        public string $nodePackageManager,
    ) {}
}
```

**Properties:**

| Property | Type | Description |
|----------|------|-------------|
| `$phpRequire` | `array` | Production PHP package names |
| `$phpRequireDev` | `array` | Development PHP package names |
| `$nodeDevDependencies` | `array` | Node package names |
| `$installWorkflows` | `bool` | Install GitHub workflows |
| `$nodePackageManager` | `string` | npm, pnpm, yarn, or bun |

**Usage:**
```php
$selection = new PackageSelection(
    phpRequire: ['spatie/laravel-query-builder'],
    phpRequireDev: ['pestphp/pest'],
    nodeDevDependencies: ['release-it'],
    installWorkflows: true,
    nodePackageManager: 'npm',
);
```

### ComposerScripts

**Location:** `src/DTOs/ComposerScripts.php`

Defines composer scripts to add to `composer.json`.

```php
readonly class ComposerScripts
{
    public static function getScripts(): array
    {
        return [
            'test' => ['@test:type-coverage', '@test:unit'],
            'test:unit' => 'pest --parallel --coverage --min=100',
            'test:types' => 'phpstan analyse',
            'lint' => ['rector', 'pint --parallel'],
            'pint' => 'pint --parallel',
            // ... more scripts
        ];
    }
}
```

**Script Categories:**

**Testing:**
- `test` - Run all tests
- `test:unit` - Unit tests only
- `test:types` - Static analysis
- `test:type-coverage` - Type coverage analysis
- `test:lint` - Code quality checks

**Linting/Formatting:**
- `lint` - All checks at once
- `pint` - Code formatting only
- `rector` - Code refactoring only

**Architecture:**
- `test:arch` - Architecture tests only

Each script is fully typed and documented.

## Enums

Type-safe package definitions as PHP enums.

### PhpPackage

**Location:** `src/Enums/PhpPackage.php`

Production PHP packages (not dev-only).

```php
enum PhpPackage: string
{
    case ESSENTIALS = 'nunomaduro/essentials';
    case AUTH_LOGS = 'akira/laravel-auth-logs';

    public function label(): string
    {
        return match ($this) {
            self::ESSENTIALS => 'Nunomaduro Essentials',
            self::AUTH_LOGS => 'Auth Logs',
        };
    }

    public static function toOptions(): array
    {
        return [
            self::ESSENTIALS->value => self::ESSENTIALS->label(),
            self::AUTH_LOGS->value => self::AUTH_LOGS->label(),
        ];
    }
}
```

**Methods:**
- `label()` - Human-readable label for UI
- `toOptions()` - Array for multiselect prompts
- `allValues()` - All enum values

**Packages:**
1. **Nunomaduro Essentials** - Essential Laravel helpers
2. **Auth Logs** - Authentication event logging

### PhpDevPackage

**Location:** `src/Enums/PhpDevPackage.php`

Development PHP packages (dev dependencies only).

14 packages across categories:

**Testing (5):**
- Pest
- Pest Laravel
- Pest Browser
- Mockery
- Faker

**Code Quality (3):**
- Laravel Pint
- Rector
- Larastan

**Debugging (3):**
- Akira Debugger
- Laravel Pail
- Collision

**Tools (2):**
- Laravel Boost
- Orchestra Testbench

**Security (1):**
- Roave Security Advisories

### NodeDevPackage

**Location:** `src/Enums/NodeDevPackage.php`

Node.js development packages.

```php
enum NodeDevPackage: string
{
    case RELEASE_IT = 'release-it';
    case COMMITLINT = '@commitlint/cli';
    case COMMITLINT_CONFIG = '@commitlint/config-conventional';
    case CONVENTIONAL_CHANGELOG = '@release-it/conventional-changelog';

    // ... methods same as PHP packages
}
```

**Packages:**
1. **Release-It** - Automated release management
2. **Commitlint** - Commit message validation
3. **Commitlint Config** - Conventional commits standard
4. **Conventional Changelog** - Automatic changelog generation

## Support Classes

### PackageDetector

**Location:** `src/Support/PackageDetector.php`

Detects system capabilities and installed package managers.

```php
final class PackageDetector
{
    public function __construct(private ExecutableFinder $finder) {}

    public function detectNodePackageManager(): string
    {
        $managers = ['bun', 'pnpm', 'yarn', 'npm'];

        foreach ($managers as $manager) {
            if ($this->finder->find($manager)) {
                return $manager;
            }
        }

        return 'npm';
    }

    public function getInstallCommand(string $manager): string
    {
        return match ($manager) {
            'npm' => 'npm install -D',
            'pnpm' => 'pnpm add -D',
            'yarn' => 'yarn add -D',
            'bun' => 'bun add -d',
        };
    }

    public function hasComposerJson(): bool
    {
        return file_exists(base_path('composer.json'));
    }

    public function hasPackageJson(): bool
    {
        return file_exists(base_path('package.json'));
    }
}
```

**Methods:**

| Method | Returns | Purpose |
|--------|---------|---------|
| `detectNodePackageManager()` | `string` | Auto-detect installed manager |
| `getInstallCommand()` | `string` | Get install command for manager |
| `hasComposerJson()` | `bool` | Check for composer.json |
| `hasPackageJson()` | `bool` | Check for package.json |

**Detection Order:** bun → pnpm → yarn → npm

Uses Symfony's `ExecutableFinder` to check system PATH.

### FileManager

**Location:** `src/Support/FileManager.php`

Wraps Laravel's Filesystem for project operations.

```php
final class FileManager
{
    public function __construct(private Filesystem $filesystem) {}

    public function copyStub(string $stub, string $destination): void
    {
        $content = $this->filesystem->get(
            dirname(__DIR__) . "/../stubs/{$stub}"
        );

        $this->filesystem->put($destination, $content);
    }

    public function updateJson(string $path, array $data): void
    {
        $current = json_decode(
            $this->filesystem->get($path),
            associative: true
        );

        $merged = $this->merge($current, $data);

        $this->filesystem->put(
            $path,
            json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );
    }

    public function mergeComposerScripts(array $scripts): void
    {
        $this->updateJson('composer.json', [
            'scripts' => $scripts,
        ]);
    }

    public function addPackageJsonScripts(array $scripts): void
    {
        // Only add if package.json exists
        if (! $this->filesystem->exists('package.json')) {
            return;
        }

        $this->updateJson('package.json', [
            'scripts' => $scripts,
        ]);
    }

    private function merge(array $array1, array $array2): array
    {
        // Recursive array merge
    }
}
```

**Methods:**

| Method | Purpose |
|--------|---------|
| `copyStub()` | Copy stub file to project |
| `updateJson()` | Update JSON file with array merge |
| `mergeComposerScripts()` | Add scripts to composer.json |
| `addPackageJsonScripts()` | Add scripts to package.json |

**Smart Features:**
- Recursive array merging
- JSON pretty-printing
- Automatic directory creation
- Preserves existing data while merging

---

**← Previous:** [02 - Architecture Overview](./02-architecture-overview.md) | **Next:** [04 - Setup Command →](./04-setup-command.md)
