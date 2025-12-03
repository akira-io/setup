# Testing Guide

Comprehensive guide to testing in Akira Setup, running tests, and adding new tests.

## Testing Framework

Akira Setup uses **Pest** as the testing framework with 100% type coverage requirement.

### Why Pest?

- **Clean Syntax** - Readable, expressive test code
- **Type-Safe** - Full type hint support with coverage checking
- **Fast** - Parallel execution support
- **Modern** - Built for PHP 8+
- **Laravel Integration** - Native Laravel testing helpers

## Running Tests

### Run All Tests

```bash
composer test
```

Runs:
- Type coverage check (minimum 100%)
- All unit and feature tests
- Code quality validation

### Run Specific Test Categories

**Unit Tests Only**
```bash
composer test:unit
```

Runs all tests in `tests/Unit/` directory.

**Type Coverage Only**
```bash
composer test:type-coverage
```

Shows type coverage report. Must be at least 100%.

**Code Quality Checks**
```bash
composer test:lint
```

Runs:
- Pint (code style)
- Rector (refactoring suggestions)
- PHPStan (static analysis)
- Larastan (Laravel static analysis)

**Architecture Tests**
```bash
composer test:arch
```

Runs architecture rules (ensures no debugging functions).

## Test Structure

### Directory Layout

```
tests/
├── ArchTest.php                           # Architecture validation
├── Feature/
│   ├── SetupCommandTest.php               # Command structure tests
│   └── SetupCommandIntegrationTest.php    # Integration tests
├── Unit/
│   ├── Actions/                           # Action tests
│   │   ├── InstallNodePackagesActionTest.php
│   │   ├── InstallPhpPackagesActionTest.php
│   │   ├── PublishConfigFilesActionTest.php
│   │   └── PublishWorkflowsActionTest.php
│   ├── Console/Concerns/                  # Trait tests
│   │   ├── CollectsUserChoicesTest.php
│   │   ├── InstallsPackagesTest.php
│   │   ├── PublishesAssetsTest.php
│   │   └── ConcernsTest.php
│   ├── Support/
│   │   └── FileManagerTest.php            # File operations
│   ├── ComposerScriptsTest.php
│   ├── EnumsTest.php
│   ├── PackageDetectorTest.php
│   └── TraitsTest.php
└── Pest.php                               # Pest configuration
```

### Test Organization

**Feature Tests** (`tests/Feature/`)
- Test command structure and registration
- Test integration between components
- Test full workflows

**Unit Tests** (`tests/Unit/`)
- Test individual actions
- Test utility classes
- Test data structures
- Test traits in isolation

**Architecture Tests** (`tests/ArchTest.php`)
- Validate no debugging functions
- Check class structure
- Verify design patterns

## Test Examples

### Feature: SetupCommand Test

Location: `tests/Feature/SetupCommandTest.php`

Tests command structure and dependencies:

```php
test('setup command is registered')
    ->artisan('akira:setup')
    ->assertSuccessful();

test('setup command has correct signature')
    ->assertThat(SetupCommand::class)
    ->hasSignature('akira:setup');

test('setup command injects all required dependencies')
    ->assertThat(SetupCommand::class)
    ->hasDependencies([
        PackageDetector::class,
        FileManager::class,
        InstallPhpPackagesAction::class,
        InstallNodePackagesAction::class,
        PublishConfigFilesAction::class,
        PublishWorkflowsAction::class,
    ]);
```

### Unit: FileManager Test

Location: `tests/Unit/Support/FileManagerTest.php`

Tests file operations:

```php
test('copy stub copies file from stubs directory to destination', function () {
    $this->fileManager->copyStub('pint.json', 'pint.json');

    expect(file_exists('pint.json'))->toBeTrue();
});

test('update json merges arrays recursively', function () {
    file_put_contents('composer.json', json_encode(['scripts' => ['test' => 'old']]));

    $this->fileManager->updateJson('composer.json', ['scripts' => ['test' => 'new']]);

    $content = json_decode(file_get_contents('composer.json'), true);
    expect($content['scripts']['test'])->toBe('new');
});

test('merge composer scripts adds scripts without overwriting existing', function () {
    file_put_contents('composer.json', json_encode(['scripts' => ['custom' => 'custom-command']]));

    $this->fileManager->mergeComposerScripts(['test' => 'pest']);

    $content = json_decode(file_get_contents('composer.json'), true);
    expect($content['scripts'])->toHaveKey('test')
        ->and($content['scripts'])->toHaveKey('custom');
});
```

### Unit: Action Test

Location: `tests/Unit/Actions/InstallPhpPackagesActionTest.php`

Tests package installation:

```php
test('install php packages executes composer require command', function () {
    $action = new InstallPhpPackagesAction($mockProcess);

    $result = $action->handle(['package1', 'package2']);

    expect($mockProcess->command)
        ->toBe('composer require package1 package2');
});

test('install dev packages uses require-dev flag', function () {
    $action = new InstallPhpPackagesAction($mockProcess);

    $result = $action->handle(['dev-package'], isDev: true);

    expect($mockProcess->command)
        ->toContain('require-dev');
});
```

### Unit: Enum Test

Location: `tests/Unit/EnumsTest.php`

Tests package definitions:

```php
test('php package enum has all expected packages', function () {
    $options = PhpPackage::toOptions();

    expect($options)->toHaveKey('nunomaduro/essentials')
        ->and($options)->toHaveKey('akira/laravel-auth-logs');
});

test('php dev package enum has correct labels', function () {
    expect(PhpDevPackage::PEST->label())
        ->toBe('Pest');
});
```

## Test Patterns Used

### Arrange-Act-Assert

Every test follows this structure:

```php
test('action returns correct result', function () {
    // Arrange: Set up test data
    $input = ['package1', 'package2'];

    // Act: Perform the action
    $result = $action->handle($input);

    // Assert: Verify the result
    expect($result)->toBeTrue();
});
```

### Expectation Chains

Use expectation chains with `->and()`:

```php
// Good
expect($value1)->toBe(true)
    ->and($value2)->toBe(false)
    ->and($value3)->toContain('text');

// Avoid
expect($value1)->toBe(true);
expect($value2)->toBe(false);
expect($value3)->toContain('text');
```

### Meaningful Setup Data

Use descriptive variable names in arrange phase:

```php
// Better: Clear intent
$adminUser = User::factory()->create(['email' => 'admin@admin.com']);
$regularUser = User::factory()->create(['email' => 'user@user.com']);

// Instead of
$user1 = User::factory()->create();
$user2 = User::factory()->create();
```

### Database Assertions

Verify database changes:

```php
test('user is created in database', function () {
    // Arrange & Act
    $user = User::create(['name' => 'Test User']);

    // Assert
    expect($user)->toBeSaved()
        ->and(User::count())->toBe(1);
});
```

## Type Coverage Requirements

All code must have 100% type coverage.

### What Counts

- Property types
- Parameter types
- Return types
- Docblock types for static analysis

### What Doesn't Count

- Comments without `@var`, `@param`, `@return`
- Un-typed variables in tests

### Checking Coverage

```bash
composer test:type-coverage
```

Shows:
- Overall coverage percentage
- Files with missing types
- Specific lines needing types

### Example: Proper Typing

```php
// Properly typed - counts toward coverage
final class Example {
    public function __construct(
        private string $name,
        private int $age,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /** @var User[] */
    private array $users = [];
}

// Missing types - reduces coverage
class BadExample {
    private $name;  // No type
    private $age;

    public function getName()  // No return type
    {
        return $this->name;
    }
}
```

## Writing New Tests

### Test Naming

Tests should follow this pattern:

```php
test('feature name describes expected behavior')
    ->expect($something)->toBe($value);

// Examples
test('pint json file is published to project root')
test('composer scripts are merged without overwriting existing')
test('node packages are installed with correct manager')
test('github workflows directory is created')
```

### Test File Naming

- Feature tests: `tests/Feature/ComponentNameTest.php`
- Unit tests: `tests/Unit/ComponentNameTest.php`
- Traits: `tests/Unit/Console/Concerns/TraitNameTest.php`
- Actions: `tests/Unit/Actions/ActionNameTest.php`

### Test Structure

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\YourAction;
use PHPUnit\Framework\Attributes\Test;

test('your action does something', function () {
    // Arrange
    $input = ['data'];
    $action = new YourAction();

    // Act
    $result = $action->handle($input);

    // Assert
    expect($result)->toBeTrue();
});
```

### Testing Actions

```php
test('action executes and returns boolean', function () {
    $action = new YourAction($dependency);

    $result = $action->handle($data);

    expect($result)->toBeTrue();
});

test('action throws exception on invalid input', function () {
    $action = new YourAction($dependency);

    expect(fn () => $action->handle([]))
        ->toThrow(InvalidArgumentException::class);
});
```

### Testing FileManager

```php
test('file operations preserve existing data', function () {
    // Setup initial file
    file_put_contents('test.json', json_encode(['existing' => 'data']));

    // Perform operation
    $fileManager->updateJson('test.json', ['new' => 'data']);

    // Verify
    $content = json_decode(file_get_contents('test.json'), true);
    expect($content)->toHaveKeys(['existing', 'new']);
});
```

### Testing Commands

```php
test('command runs successfully', function () {
    $this->artisan('command:name')
        ->assertSuccessful();
});

test('command displays correct output', function () {
    $this->artisan('command:name')
        ->expectsOutput('Expected message')
        ->assertSuccessful();
});

test('command creates required files', function () {
    $this->artisan('command:name')
        ->assertSuccessful();

    expect(file_exists('expected-file.json'))->toBeTrue();
});
```

## Architecture Tests

Location: `tests/ArchTest.php`

Validates code quality rules:

```php
// Prevents debugging functions
arch('no debugging functions')
    ->expect([
        'dd', 'dump', 'ray', 'ad', 'var_dump'
    ])
    ->not->toBeUsed();

// Ensures final classes where appropriate
arch('actions are final')
    ->expect('App\\Actions\\')
    ->toBeReadonly();

// Validates traits have specific pattern
arch('concerns are traits')
    ->expect('App\\Console\\Concerns\\')
    ->toBeTraits();
```

These tests prevent common issues from being committed.

## Continuous Integration

### GitHub Actions Workflow

The `.github/workflows/tests.yml` file runs tests automatically:

```yaml
- name: Run Tests
  run: composer test

- name: Check Type Coverage
  run: composer test:type-coverage

- name: Code Quality
  run: composer test:lint
```

Tests run on:
- PHP 8.4
- Laravel 12
- All supported scenarios

### Pre-commit Checks

Consider running tests before committing:

```bash
# Before commit
composer test
composer test:lint
```

## Debugging Tests

### Verbose Output

```bash
php artisan test --verbose
```

Shows test names and timing.

### Single Test File

```bash
php artisan test tests/Unit/Actions/YourActionTest.php
```

### Single Test

```bash
php artisan test tests/Unit/Actions/YourActionTest.php --filter="test_name"
```

### With Coverage

```bash
php artisan test --coverage
```

Shows code coverage percentage.

## Performance

### Parallel Execution

Tests run in parallel by default:

```bash
composer test
```

### Disable Parallel

```bash
php artisan test --no-parallel
```

### Optimize Speed

1. Use SQLite in-memory database for tests
2. Run database-heavy tests separately
3. Use factories efficiently
4. Avoid unnecessary file I/O

## Best Practices

1. **Test One Thing** - Each test validates one behavior
2. **Clear Names** - Test names describe what they verify
3. **No Testing Framework Leaks** - Tests don't expose framework details
4. **Deterministic** - Tests produce same result every run
5. **Fast** - Tests complete quickly
6. **Independent** - Tests don't depend on each other
7. **Type Safe** - All code is properly typed
8. **100% Type Coverage** - Every line has types

## Troubleshooting

### Tests Fail Due to File System

**Issue:** Tests fail when cleanup doesn't work

**Solution:**
```php
beforeEach(function () {
    // Setup temp files
});

afterEach(function () {
    // Clean up temp files
    File::deleteDirectory(storage_path('test'));
});
```

### Type Coverage Issues

**Issue:** "Type coverage below 100%"

**Solution:** Add proper type hints

```php
// Before
private $data;

// After
/** @var array<string, mixed> */
private array $data;
```

### Mock Not Working

**Issue:** Mock doesn't intercept calls

**Solution:** Inject through constructor

```php
$mock = mock(Service::class);

$action = new Action($mock);
$action->handle();
```

### Test Isolation Issues

**Issue:** Tests affect each other

**Solution:** Use `beforeEach()` and `afterEach()`

```php
beforeEach(function () {
    $this->fileManager = new FileManager();
    mkdir('test-dir');
});

afterEach(function () {
    rmdir('test-dir');
});
```

---

**← Previous:** [04 - Setup Command](./04-setup-command.md) | **Next:** [06 - Contributing →](./06-contributing.md)
