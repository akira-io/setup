# Contributing Guide

Thank you for considering contributing to Akira Setup! This guide explains how to contribute to the project.

## Getting Started

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork locally:

```bash
git clone https://github.com/your-username/setup.git
cd setup
```

3. Add upstream remote:

```bash
git remote add upstream https://github.com/akira/setup.git
```

### Install Dependencies

```bash
composer install
npm install
```

### Create a Feature Branch

```bash
git checkout -b feature/your-feature-name
```

Use descriptive branch names:
- `feature/add-new-package`
- `fix/action-bug`
- `docs/improve-readme`

## Code Standards

Akira Setup follows strict code quality standards.

### PHP Standards

- **Strict Types** - Add `declare(strict_types=1)` to all files
- **PHP 8.4 Features** - Use constructor promotion, match, readonly
- **Full Type Hints** - Every parameter and return type must be typed
- **PSR-12** - Follow PSR-12 code style
- **PHPStan Level Max** - Pass Larastan maximum level

### Naming Conventions

- **Classes** - PascalCase (e.g., `SetupCommand`)
- **Methods** - camelCase (e.g., `collectUserChoices()`)
- **Properties** - camelCase (e.g., `$packageDetector`)
- **Constants** - UPPER_SNAKE_CASE (e.g., `MAX_TIMEOUT`)

### Code Organization

**Actions:**
- Located in `src/Actions/`
- Single public method: `handle()`
- Fully dependency injected
- No side effects

**Traits (Concerns):**
- Located in `src/Console/Concerns/`
- Named descriptively (e.g., `CalculatesSetupSteps`)
- Single responsibility

**DTOs:**
- Located in `src/DTOs/`
- Readonly with immutable properties
- No methods (except static factory)

**Enums:**
- Located in `src/Enums/`
- Define packages/options
- Provide `label()` method

**Support Classes:**
- Located in `src/Support/`
- Utility functions
- Reusable across application

### Type Hints

Use full type hints everywhere:

```php
final readonly class PublishConfigFilesAction
{
    public function __construct(private FileManager $fileManager) {}

    public function handle(PackageSelection $selection): void
    {
        // Proper types everywhere
    }
}
```

## Adding Features

### Adding a New Package

To add a new package option (e.g., new PHP dev package):

**1. Update the Enum**

Location: `src/Enums/PhpDevPackage.php`

```php
enum PhpDevPackage: string
{
    // ... existing cases ...
    case NEW_PACKAGE = 'vendor/new-package';

    public function label(): string
    {
        return match ($this) {
            // ... existing ...
            self::NEW_PACKAGE => 'New Package Label',
        };
    }
}
```

**2. No Other Changes Needed**

The rest of the system automatically picks up the new option:
- The prompt will show it
- It will be installable
- Tests should cover it

**3. Add Tests**

Location: `tests/Unit/EnumsTest.php`

```php
test('new package enum has correct label', function () {
    expect(PhpDevPackage::NEW_PACKAGE->label())
        ->toBe('New Package Label');
});
```

### Adding a New Action

**1. Create the Action**

Location: `src/Actions/YourAction.php`

```php
<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\PackageSelection;

final readonly class YourAction
{
    public function __construct(private FileManager $fileManager) {}

    public function handle(PackageSelection $selection): void
    {
        // Your logic here
    }
}
```

**2. Add Tests**

Location: `tests/Unit/Actions/YourActionTest.php`

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\YourAction;

test('action does something', function () {
    $action = new YourAction($fileManager);

    $action->handle($selection);

    expect(/* assertion */)->toBeTrue();
});
```

**3. Integrate with SetupCommand**

Add to SetupCommand if needed:
- Inject in constructor
- Call from appropriate trait method

### Adding a Configuration File

**1. Create the Stub**

Location: `stubs/your-config.json`

```json
{
  "setting": "value"
}
```

**2. Update PublishConfigFilesAction**

Location: `src/Actions/PublishConfigFilesAction.php`

```php
public function handle(PackageSelection $selection): void
{
    $this->fileManager->copyStub('your-config.json', 'your-config.json');
    // ... other files ...
}
```

**3. Add Tests**

Verify the file is published in tests.

## Testing Requirements

### Test Every Change

All code changes must include tests:

```bash
composer test
```

Must pass:
- All tests (unit and feature)
- Type coverage (100%)
- Code quality checks

### Write Tests First

For bugs: Reproduce with a test, then fix.

```php
test('bug is fixed', function () {
    // Reproduce the bug
    $result = $broken->method();

    // Verify fix
    expect($result)->toBeCorrect();
});
```

For features: Write test before code.

```php
test('new feature works', function () {
    // Act
    $result = newFeature->handle($data);

    // Assert expected behavior
    expect($result)->toMatch($expected);
});
```

### Test Coverage

Ensure 100% type coverage:

```bash
composer test:type-coverage
```

If you see:
```
Type coverage: 99.5% (199/200 lines)
```

Add types to the missing lines.

## Code Quality

### Run All Checks

```bash
composer test
composer test:lint
```

### Fix Code Style

```bash
composer pint
```

### Suggest Refactorings

```bash
composer rector
```

### Static Analysis

```bash
composer phpstan
```

Fix all issues reported by PHPStan.

### Architecture Tests

```bash
composer test:arch
```

Ensures no debugging functions (dd, dump, etc.).

## Git Workflow

### Before Committing

```bash
# Run all tests
composer test

# Fix style issues
composer pint

# Check type coverage
composer test:type-coverage

# Static analysis
composer phpstan
```

### Commit Messages

Use clear, descriptive commit messages:

```
feat: add new package option

- Add package to PhpDevPackage enum
- Add label for package selection
- Add tests for new package
```

Reference issues when applicable:

```
fix: resolve issue with file permissions

Closes #123
```

### Pull Request

1. **Title**: Clear description of change
2. **Description**: What changed and why
3. **Tests**: Reference tests added/updated
4. **Breaking Changes**: Note if any breaking changes

## Project Structure Maintenance

### Keep Structure Organized

When adding files:
- Actions go in `src/Actions/`
- Traits go in `src/Console/Concerns/`
- Tests mirror source structure
- Stubs go in `stubs/`

### Update Documentation

If changing functionality:
1. Update relevant markdown file
2. Update code comments if needed
3. Update tests examples
4. Update CHANGELOG

## Common Contributions

### Fix a Bug

1. Create a test that reproduces the bug
2. Make the test pass
3. Ensure all tests still pass
4. Submit PR with before/after behavior

### Add a Package Option

1. Add enum case
2. Add label
3. Add test
4. Verify in manual testing

### Improve Performance

1. Measure the improvement
2. Add test to verify performance
3. Document the optimization

### Improve Documentation

1. Update markdown file
2. Add examples if applicable
3. Check links work
4. Verify formatting

## Debugging

### Enable Verbose Output

```bash
php artisan akira:setup --verbose
```

### Check File Operations

Add temporary logging:

```php
ray('File content:', $content);  // Will be cleaned before commit
```

Note: `ray()` and similar are caught by architecture tests, so remove before commit.

### Test Specific Scenario

Create a test that reproduces the issue:

```php
test('specific scenario works', function () {
    $this->setup->execute($specificData);

    expect(/* assertion */)->toBeTrue();
});
```

## Review Process

When you submit a PR:

1. **Code Review** - Code is reviewed for standards and patterns
2. **Tests** - All tests must pass
3. **Coverage** - Type coverage must be 100%
4. **Documentation** - Updates are documented
5. **Approval** - PR is approved when ready

## Questions?

- Check the architecture documentation
- Review similar code patterns in the codebase
- Look at existing tests for examples
- Ask in the PR discussion

## Code of Conduct

- Be respectful and inclusive
- Focus on the code, not the person
- Help others learn and improve
- Report issues professionally

## License

By contributing, you agree that your contributions will be licensed under the same license as the project (MIT License).

## Helpful Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Pest Documentation**: https://pestphp.com
- **PHPStan Documentation**: https://phpstan.org
- **Rector Documentation**: https://getrector.org
- **Laravel Pint Documentation**: https://laravel.com/docs/pint

## Release Checklist

When preparing a release:

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Run full test suite: `composer test`
4. Create git tag: `git tag v1.0.0`
5. Push to main branch
6. Create GitHub release
7. Announce in relevant channels

---

**← Previous:** [05 - Testing Guide](./05-testing-guide.md)

Thank you for contributing to Akira Setup!
