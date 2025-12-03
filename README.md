# Akira Setup

[![Latest Version on Packagist](https://img.shields.io/packagist/v/akira/setup.svg?style=flat-square)](https://packagist.org/packages/akira/setup)
[![Total Downloads](https://img.shields.io/packagist/dt/akira/setup.svg?style=flat-square)](https://packagist.org/packages/akira/setup)

An interactive Laravel package installer that sets up your project with best practices, testing tools, code quality
standards, and CI/CD workflows in seconds.

## Features

**Interactive Setup** - Beautiful CLI interface using Laravel Prompts  
**Smart Package Management** - Automatically installs PHP and Node.js packages  
**Auto-Detection** - Detects your preferred Node package manager (npm, pnpm, yarn, bun)  
️ **Configuration Files** - Generates Laravel Pint, Rector, and Release-It configs  
**GitHub Actions** - Installs pre-configured CI/CD workflows  
**Composer Scripts** - Adds convenient testing and linting scripts  
**Fully Typed** - PHP 8.4 with strict types and modern features  
**Tested** - Comes with Pest tests

## Requirements

- PHP 8.4+
- Laravel 12+
- Composer 2+

## Installation

Install the package via Composer:

```bash
composer require --dev akira/setup
```

The package will automatically register itself via Laravel's package auto-discovery.

## Usage

Run the interactive setup command:

```bash
php artisan akira:setup
```

The command will guide you through:

1. **Select PHP Packages** - Choose from essential Laravel packages
2. **Select Dev Packages** - Pick testing and code quality tools
3. **Select Node Packages** - Choose release management tools
4. **Package Manager** - Select npm, pnpm, yarn, or bun
5. **GitHub Workflows** - Optionally install CI/CD pipelines

## What Gets Installed

### PHP Packages (require)

- `nunomaduro/essentials` - Essential Laravel helpers
- `akira/laravel-auth-logs` - Authentication logging

### PHP Packages (require-dev)

- `akira/laravel-debugger` - Enhanced debugging tools
- `driftingly/rector-laravel` - Laravel-specific Rector rules
- `fakerphp/faker` - Fake data generator
- `larastan/larastan` - PHPStan for Laravel
- `laravel/boost` - Laravel performance optimizer
- `laravel/pail` - Log viewer
- `laravel/pint` - Code style fixer
- `mockery/mockery` - Mocking framework
- `nunomaduro/collision` - Error handler
- `pestphp/pest` - Testing framework
- `pestphp/pest-plugin-browser` - Browser testing
- `pestphp/pest-plugin-laravel` - Laravel support
- `pestphp/pest-plugin-type-coverage` - Type coverage
- `rector/rector` - PHP refactoring tool
- `roave/security-advisories` - Security vulnerability scanner

### Node.js Packages (devDependencies)

- `release-it` - Release management tool
- `@commitlint/cli` - Commit message linter
- `@commitlint/config-conventional` - Conventional commits config
- `@release-it/conventional-changelog` - Changelog generator

### Configuration Files

#### `pint.json`

Laravel Pint configuration with comprehensive rules for:

- Strict typing
- Modern PHP 8.4 features
- Final classes
- Readonly properties
- Type declarations
- Code quality standards

#### `rector.php`

Rector configuration with:

- PHP 8.4 upgrade rules
- Laravel 12 compatibility
- Code quality improvements
- Type declaration automation
- Dead code removal
- Privatization rules

#### `.release-it.json`

Release-It configuration for:

- Semantic versioning
- Conventional changelog generation
- Automatic GitHub releases
- Git tag management

### Composer Scripts

The following scripts are added/merged into `composer.json`:

```json
{
  "scripts": {
    "lint": [
      "rector",
      "pint --parallel",
      "npm run lint"
    ],
    "test:type-coverage": "pest --type-coverage --min=100",
    "test:lint": [
      "pint --parallel --test",
      "rector --dry-run",
      "npm run test:lint"
    ],
    "test:unit": "pest --parallel --coverage --exactly=100.0",
    "test:types": [
      "phpstan",
      "npm run test:types"
    ],
    "test": [
      "@test:type-coverage",
      "@test:unit",
      "@test:lint",
      "@test:types"
    ]
  }
}
```

### Package.json Scripts

```json
{
  "scripts": {
    "release": "release-it"
  }
}
```

### GitHub Actions Workflows

#### `.github/workflows/tests.yml`

Comprehensive testing pipeline with:

- PHP 8.4 test matrix
- Laravel 12 compatibility
- Multiple OS support
- Code quality checks
- Static analysis with PHPStan

#### `.github/workflows/release-discord.yml`

Automatic Discord notifications for new releases with:

- Release version
- Release notes
- Repository link
- Timestamp

## Usage Examples

### Running Tests

```bash
# Run all tests with type coverage, unit tests, linting, and type checking
composer test

# Run only unit tests
composer test:unit

# Run only type coverage
composer test:type-coverage

# Run only linting
composer test:lint

# Run only type checking
composer test:types
```

### Code Quality

```bash
# Auto-fix code style and refactor
composer lint

# Check code style without fixing
pint --test

# Run Rector dry-run
rector --dry-run
```

### Creating Releases

```bash
# Interactive release (follows semantic versioning)
npm run release

# Specific version
npm run release -- --increment patch
npm run release -- --increment minor
npm run release -- --increment major
```

## Architecture

The package follows Spatie-level quality standards:

- **Action Pattern** - Business logic isolated in action classes
- **DTOs** - Data Transfer Objects for complex data structures
- **Service Provider** - Auto-discovery for seamless integration
- **Dependency Injection** - No facades, pure DI
- **Strict Typing** - PHP 8.4 with `strict_types=1`
- **Final Classes** - Immutability where appropriate
- **Readonly Properties** - Modern PHP features
- **No Static Calls** - Testable, maintainable code

### Package Structure

```
akira/setup/
├── src/
│   ├── Console/
│   │   └── SetupCommand.php          # Main interactive command
│   ├── Actions/
│   │   ├── InstallPhpPackagesAction.php
│   │   ├── InstallNodePackagesAction.php
│   │   ├── PublishConfigFilesAction.php
│   │   └── PublishWorkflowsAction.php
│   ├── DTOs/
│   │   ├── PackageSelection.php
│   │   └── ComposerScripts.php
│   ├── Support/
│   │   ├── PackageDetector.php       # Package manager detection
│   │   └── FileManager.php           # File operations
│   └── AkiraSetupServiceProvider.php
├── stubs/
│   ├── pint.json
│   ├── rector.php
│   ├── release-it.json
│   └── github/
│       ├── release-discord.yml
│       └── tests.yml
└── tests/
    ├── Feature/
    │   └── SetupCommandTest.php
    └── Unit/
        ├── PackageDetectorTest.php
        └── ComposerScriptsTest.php
```

## Testing

Run the package tests:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Akira](https://github.com/akira)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
