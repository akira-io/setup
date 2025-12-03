# Setup Command Guide

Complete guide to the `akira:setup` command, its flow, and all customization options.

## Command Overview

The `akira:setup` command is the main entry point for initializing a Laravel project with best practices.

### Basic Usage

```bash
php artisan akira:setup
```

No arguments or options are required. The command is fully interactive and guides you through each step.

### Command Signature

```
Command: akira:setup
Description: Set up your Laravel project with industry best practices
```

## Complete Setup Flow

### Step 1: Validation

The command first validates that you're in a Laravel project:

```
✓ Checking for composer.json
✓ Project structure validated
```

If `composer.json` doesn't exist, the command exits with an error message.

### Step 2: PHP Production Packages

You're prompted to select production PHP packages:

```
Which production PHP packages would you like to install?

  [0] ○ Nunomaduro Essentials
  [1] ○ Auth Logs

Select with SPACE, confirm with ENTER:
```

**Available Options:**

| Package | Label | Purpose |
|---------|-------|---------|
| `nunomaduro/essentials` | Nunomaduro Essentials | Laravel helpers and utilities |
| `akira/laravel-auth-logs` | Auth Logs | Authentication logging |

**Default:** All selected (indicated by filled checkboxes)

**Selection Tips:**
- Use SPACE to toggle selections
- Use UP/DOWN arrows to navigate
- Press ENTER to confirm
- You can deselect all if you want

### Step 3: PHP Development Packages

Select development PHP packages:

```
Which development PHP packages would you like to install?

  [0] ○ Pest
  [1] ○ Pest Laravel
  [2] ○ Pest Browser
  [3] ○ Pest Type Coverage
  [4] ○ Mockery
  ...
```

**Available Options (14 total):**

**Testing (5):**
- Pest - Modern testing framework
- Pest Laravel - Laravel integration
- Pest Browser - Browser testing
- Pest Type Coverage - Type coverage plugin
- Mockery - Mocking library
- Faker - Fake data generation

**Code Quality (3):**
- Laravel Pint - Code style fixer
- Rector - PHP refactoring
- Larastan - Static analysis

**Debugging (3):**
- Akira Debugger - Enhanced debugging
- Laravel Pail - Log viewer
- Collision - Error display

**Tools (2):**
- Laravel Boost - Performance suggestions
- Orchestra Testbench - Testing utilities

**Security (1):**
- Roave Security Advisories - Vulnerability checker

**Default:** All selected

### Step 4: Node Package Manager Detection

If `package.json` exists, the command auto-detects your Node package manager:

```
Detected package managers:
  ✓ npm (5 minutes ago)

Use detected npm? (yes/no) [yes]:
```

**Detection Order:** bun → pnpm → yarn → npm

**Options:**
- `yes` - Use detected manager
- `no` - Choose from available managers

If you choose `no`, you'll see:

```
Which package manager would you like to use?

  [0] npm
  [1] pnpm
  [2] yarn
  [3] bun

Select one:
```

### Step 5: Node Development Packages

If `package.json` exists, select Node packages:

```
Which Node development packages would you like to install?

  [0] ○ Release-It
  [1] ○ Commitlint
  [2] ○ Commitlint Config
  [3] ○ Conventional Changelog

Select with SPACE, confirm with ENTER:
```

**Available Options:**

| Package | Purpose |
|---------|---------|
| `release-it` | Automated release management |
| `@commitlint/cli` | Commit message validation |
| `@commitlint/config-conventional` | Conventional commits standard |
| `@release-it/conventional-changelog` | Changelog generation |

**Default:** All selected

### Step 6: GitHub Workflows

Confirm GitHub Actions installation:

```
Install GitHub Actions workflows and templates? (yes/no) [yes]:
```

**What Gets Installed (if yes):**
- `.github/workflows/tests.yml` - CI/CD testing
- `.github/workflows/release-discord.yml` - Release notifications
- `.github/dependabot.yml` - Dependency updates
- `.github/FUNDING.yml` - GitHub Sponsors
- `.github/issue_template/bug.yml` - Bug template
- `.github/issue_template/feature.yml` - Feature template

**Default:** Yes

## Installation Process

After you confirm your selections, the command executes the following steps (shown with progress bar):

### Step: php-require

**Condition:** Runs if you selected production PHP packages

```
Installing PHP packages...
Running: composer require nunomaduro/essentials akira/laravel-auth-logs
```

**What Happens:**
- Executes `composer require [packages]`
- Uses default Composer settings
- Timeout: 600 seconds
- Adds packages to `require` in composer.json

**Output:**
```
✓ PHP packages installed
```

### Step: php-require-dev

**Condition:** Runs if you selected dev PHP packages

```
Installing PHP development packages...
Running: composer require --dev pestphp/pest laravel/pint ...
```

**What Happens:**
- Executes `composer require --dev [packages]`
- Adds packages to `require-dev` in composer.json
- May take several minutes

**Output:**
```
✓ PHP development packages installed
```

### Step: node-packages

**Condition:** Runs if you selected Node packages and have package.json

```
Installing Node packages...
Running: npm install -D release-it @commitlint/cli ...
```

**What Happens:**
- Detects your package manager (npm, pnpm, yarn, or bun)
- Executes install command specific to your manager
- Adds packages to `devDependencies` in package.json

**Supported Managers:**
- `npm install -D`
- `pnpm add -D`
- `yarn add -D`
- `bun add -d`

**Output:**
```
✓ Node packages installed
```

### Step: composer-scripts

**Condition:** Always runs

```
Adding Composer scripts...
```

**What Gets Added to composer.json:**

```json
{
  "scripts": {
    "test": ["@test:type-coverage", "@test:unit"],
    "test:unit": "pest --parallel --coverage --min=100",
    "test:types": "phpstan analyse",
    "test:type-coverage": "pest --type-coverage --min=100",
    "test:lint": ["pint --parallel --test", "rector --dry-run", "..."],
    "lint": ["rector", "pint --parallel", "..."],
    "pint": "pint --parallel",
    "pint:test": "pint --parallel --test",
    "rector": "rector",
    "test:refactor": "rector --dry-run",
    "test:arch": "pest --type=arch",
    "phpstan": "phpstan analyse"
  }
}
```

**Script Categories:**

**Testing:**
- `composer test` - Run all tests
- `composer test:unit` - Unit tests only
- `composer test:types` - Static analysis
- `composer test:type-coverage` - Type coverage report
- `composer test:lint` - Code quality checks
- `composer test:arch` - Architecture tests

**Code Quality:**
- `composer lint` - All quality checks
- `composer pint` - Format code
- `composer pint:test` - Check formatting only
- `composer rector` - Show refactoring suggestions
- `composer test:refactor` - Dry-run refactoring
- `composer phpstan` - Static analysis

**How It Works:**
- Merges with existing scripts (doesn't overwrite)
- Only adds/updates specified scripts
- Preserves your custom scripts

**Output:**
```
✓ Composer scripts added
```

### Step: package-json-scripts

**Condition:** Always runs

```
Adding package.json scripts...
```

**What Gets Added to package.json:**

```json
{
  "scripts": {
    "release": "release-it"
  }
}
```

**Only if package.json exists.**

**Output:**
```
✓ Package.json scripts added
```

### Step: config-files

**Condition:** Only if workflows confirmed

```
Publishing configuration files...
```

**Files Published:**

1. **pint.json** (140+ rules)
   - PHP 8.4 strict typing
   - Modern code style enforcement
   - Array syntax rules
   - Docblock formatting

2. **rector.php** (50+ rules)
   - PHP 8.4 upgrade path
   - Laravel 12 compatibility
   - Dead code removal
   - Type declaration automation

3. **.release-it.json**
   - Semantic versioning
   - Conventional changelog
   - GitHub release automation

**Output:**
```
✓ Configuration files published
```

### Step: workflows

**Condition:** Only if workflows confirmed

```
Publishing GitHub workflows...
```

**Files Published:**

**Workflows (in .github/workflows/):**
- `tests.yml` - Run tests on push/PR
- `release-discord.yml` - Discord notifications on release

**Configuration (in .github/):**
- `dependabot.yml` - Auto-update dependencies
- `FUNDING.yml` - GitHub Sponsors setup

**Templates (in .github/issue_template/):**
- `bug.yml` - Standardized bug reports
- `feature.yml` - Standardized feature requests

**Output:**
```
✓ Workflows and templates published
```

## Completion Summary

After all steps, you see a summary:

```
╔════════════════════════════════════════╗
║      Setup completed successfully!     ║
╚════════════════════════════════════════╝

Installed packages:
  • Production: 2 packages
  • Development: 14 packages
  • Node.js: 4 packages

Completed steps:
  ✓ PHP production packages
  ✓ PHP development packages
  ✓ Node packages
  ✓ Composer scripts
  ✓ Package.json scripts
  ✓ Configuration files
  ✓ GitHub workflows

Next steps:
  composer test         # Run all tests
  composer lint         # Check code quality
  npm run release       # Release new version
```

## Detailed Option Reference

### PHP Production Packages

#### Nunomaduro Essentials
- **Package:** `nunomaduro/essentials`
- **Purpose:** Essential Laravel helpers
- **Includes:** Useful utility functions and classes
- **Size:** Small, lightweight
- **Maintenance:** Actively maintained

#### Auth Logs
- **Package:** `akira/laravel-auth-logs`
- **Purpose:** Authentication event logging
- **Includes:** Automatic logging of login/logout events
- **Size:** Small
- **Maintenance:** Actively maintained

### PHP Development Packages

#### Testing Tools

**Pest**
- **Package:** `pestphp/pest`
- **Purpose:** Modern PHP testing framework
- **Features:** Clean, readable syntax
- **Usage:** `php artisan pest` or `vendor/bin/pest`

**Pest Laravel**
- **Package:** `pestphp/pest-plugin-laravel`
- **Purpose:** Laravel integration for Pest
- **Features:** Helper methods for Laravel testing

**Pest Browser**
- **Package:** `pestphp/pest-plugin-browser`
- **Purpose:** Browser automation testing
- **Features:** Interact with JavaScript, forms, etc.

**Pest Type Coverage**
- **Package:** `pestphp/pest-plugin-type-coverage`
- **Purpose:** Type coverage reporting
- **Features:** Ensures proper type hints

**Mockery**
- **Package:** `mockery/mockery`
- **Purpose:** Mocking library
- **Features:** Create and verify mock objects

**Faker**
- **Package:** `fakerphp/faker`
- **Purpose:** Generate fake data
- **Features:** Create test data easily

#### Code Quality Tools

**Laravel Pint**
- **Package:** `laravel/pint`
- **Purpose:** Code style fixer
- **Usage:** `composer pint`
- **Config:** `pint.json`

**Rector**
- **Package:** `rectorphp/rector`
- **Purpose:** PHP refactoring
- **Usage:** `composer rector`
- **Config:** `rector.php`

**Larastan**
- **Package:** `nunomaduro/larastan`
- **Purpose:** Static analysis for Laravel
- **Usage:** `composer phpstan`
- **Integrates:** With PHPStan for strict analysis

#### Debugging & Development

**Akira Debugger**
- **Package:** `akira/laravel-debugger`
- **Purpose:** Enhanced debugging experience
- **Features:** Better error visualization

**Laravel Pail**
- **Package:** `laravel/pail`
- **Purpose:** Real-time log viewer
- **Usage:** `php artisan pail`

**Collision**
- **Package:** `nunomaduro/collision`
- **Purpose:** Beautiful error handling
- **Features:** Enhanced error messages

#### Tools

**Laravel Boost**
- **Package:** `nnjeim/laravel-boost`
- **Purpose:** Performance suggestions
- **Features:** Optimization recommendations

**Orchestra Testbench**
- **Package:** `orchestra/testbench`
- **Purpose:** Testing utilities for packages
- **Usage:** Required for package development

#### Security

**Roave Security Advisories**
- **Package:** `roave/security-advisories`
- **Purpose:** Security vulnerability checking
- **Usage:** Automatic on `composer update`

### Node Development Packages

#### Release-It
- **Package:** `release-it`
- **Purpose:** Automated release management
- **Usage:** `npm run release`
- **Features:** Version bumping, changelog, Git tags

#### Commitlint
- **Package:** `@commitlint/cli`
- **Purpose:** Commit message validation
- **Usage:** Git hook (auto-configured)
- **Features:** Enforce conventional commits

#### Commitlint Config
- **Package:** `@commitlint/config-conventional`
- **Purpose:** Conventional commits standard
- **Features:** Defines commit format rules

#### Conventional Changelog
- **Package:** `@release-it/conventional-changelog`
- **Purpose:** Generate changelog automatically
- **Usage:** Integrated with Release-It
- **Features:** Parse conventional commits to changelog

## Advanced Usage

### Customizing Selections

You can run the command multiple times and select different packages each time. The command will:
- Update your composer.json and package.json
- Overwrite configuration files
- Preserve your project code

### Selective Installation

You don't need to install everything. For example:
- Install only testing tools
- Install only code quality tools
- Install only production packages
- Mix and match as needed

### Manual Modifications

After setup, you can manually edit:
- `pint.json` - Adjust code style rules
- `rector.php` - Change refactoring rules
- `.release-it.json` - Customize release process
- `composer.json` - Remove/add scripts

### Conditional Installation

The command automatically skips steps if:
- No `package.json` exists - Node steps skipped
- You don't select workflows - GitHub Actions skipped
- You deselect all packages - Installation steps skipped

## Troubleshooting

### Command Takes Too Long

**Issue:** Composer install is taking more than 10 minutes

**Solutions:**
- Check your internet connection
- Verify Packagist is accessible
- Run `composer diagnose`
- Try `composer clear-cache`

### Permission Denied Errors

**Issue:** "Permission denied" when writing files

**Solutions:**
- Ensure project directory is writable: `chmod -R 755 .`
- Check file permissions on composer.json
- Run with appropriate user permissions

### Package Not Found

**Issue:** "Package not found" during installation

**Solutions:**
- Verify package names are correct
- Check Packagist availability
- Run `composer update` first
- Check your PHP version compatibility

### Node Manager Not Detected

**Issue:** Package manager not auto-detected

**Solutions:**
- Verify manager is installed
- Add to PATH: `which npm` or `which pnpm`
- Restart terminal to refresh PATH
- Select manually when prompted

### Setup Fails Partway

**Issue:** Setup stops with an error

**Solutions:**
- Check available disk space
- Verify internet connectivity
- Run `composer update` separately first
- Check that JSON files are valid
- Run setup again (it's safe to rerun)

## Configuration Files Explained

### pint.json

Contains 140+ Laravel Pint rules for code formatting:

```json
{
  "preset": "laravel",
  "rules": {
    "align_double_arrow": true,
    "strict_types": true,
    "declare_strict_types": true
  }
}
```

Use `composer pint` to auto-format your code.

### rector.php

Contains 50+ Rector rules for PHP refactoring:

```php
return RectorConfig::configure()
    ->withPreparedSets(codeQuality: true, typeDeclarations: true)
    ->withRules([
        // Refactoring rules
    ]);
```

Use `composer rector` to see suggested refactorings, or `composer test:refactor` for dry-run.

### .release-it.json

Configures automated releases:

```json
{
  "git": {
    "tagName": "v${version}",
    "requireCleanWorkingDir": true
  },
  "plugins": {
    "@release-it/conventional-changelog": {}
  }
}
```

Use `npm run release` to create a new release.

---

**← Previous:** [03 - Components Guide](./03-components-guide.md) | **Next:** [05 - Testing Guide →](./05-testing-guide.md)
