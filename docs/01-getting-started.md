# Getting Started with Akira Setup

This guide walks you through installing and using Akira Setup in your Laravel project.

## Prerequisites

Before you begin, ensure you have:

- **PHP 8.4 or higher**
- **Laravel 12 or higher**
- **Composer 2.0 or higher**
- **Git** (for version control)
- **Node.js** (optional, but recommended for frontend tooling)

## Installation

### Step 1: Install via Composer

Add Akira Setup to your Laravel project:

```bash
composer require akira/setup --dev
```

The package is automatically discovered by Laravel's service provider auto-discovery mechanism.

### Step 2: Run the Setup Command

Execute the interactive setup command in your project root:

```bash
php artisan akira:setup
```

This command will guide you through the setup process with interactive prompts.

## Setup Flow

When you run `php artisan akira:setup`, you'll go through these steps:

### 1. PHP Production Packages

You'll be prompted to select production PHP packages. The default includes:

- **Nunomaduro Essentials** - Essential Laravel helpers and utilities
- **Auth Logs** - Authentication event logging

You can customize this selection by deselecting packages you don't need.

### 2. PHP Development Packages

Select testing and code quality tools. Default packages include:

**Testing Tools:**
- Pest - Modern PHP testing framework
- Pest Laravel - Laravel plugin for Pest
- Pest Browser - Browser testing with Pest
- Mockery - Mocking library for tests
- Faker - Fake data generation

**Code Quality:**
- Laravel Pint - PHP code style fixer
- Rector - PHP refactoring tool
- Larastan - Static analysis for Laravel

**Debugging & Development:**
- Akira Debugger - Enhanced debugging experience
- Laravel Pail - Real-time log viewer
- Collision - Beautiful error display
- Laravel Boost - Performance optimization suggestions

**Security:**
- Roave Security Advisories - Security vulnerability checker

### 3. Node.js Packages (if applicable)

If your project has a `package.json` file, you'll be prompted to select Node.js development packages:

- **Release-It** - Automated release management
- **Commitlint** - Commit message validation
- **Conventional Changelog** - Automatic changelog generation

### 4. Node Package Manager Selection

The setup will auto-detect which package manager you have installed (checking for bun, pnpm, yarn, npm in that priority order).

You can accept the detected manager or choose a different one from available options.

### 5. GitHub Workflows

Confirm whether you want to install GitHub Actions CI/CD workflows:

- **tests.yml** - Automated testing on PHP 8.4
- **release-discord.yml** - Automated Discord notifications on release
- **dependabot.yml** - Dependency update automation
- **Issue templates** - Bug report and feature request templates
- **Funding configuration** - GitHub Sponsors setup

## After Setup

Once the setup completes, you'll see a summary showing:

- Number of PHP packages installed
- Number of development PHP packages installed
- Number of Node.js packages installed (if applicable)
- List of completed steps
- Recommended next commands

### Available Commands

After setup, you'll have these convenient commands available:

```bash
# Run all tests with type coverage and code quality checks
composer test

# Run type coverage analysis
composer test:type-coverage

# Run unit tests with coverage
composer test:unit

# Run linting and refactoring checks
composer lint
composer test:lint

# Check code style
composer pint
composer pint:test

# Run static analysis
composer phpstan

# Run Rector in dry-run mode
composer rector
composer test:refactor

# Release new version (requires Node.js)
npm run release
```

## Project Structure After Setup

Your project will now have:

```
project-root/
├── pint.json              # Code formatting configuration
├── rector.php             # PHP refactoring rules
├── .release-it.json       # Release management config
├── .github/
│   ├── workflows/
│   │   ├── tests.yml      # CI/CD testing workflow
│   │   └── release-discord.yml
│   ├── dependabot.yml     # Dependabot configuration
│   ├── FUNDING.yml        # GitHub Sponsors
│   └── issue_template/    # Issue templates (bug, feature)
└── composer.json          # Updated with test scripts
```

## Troubleshooting

### Command Not Found

If the `akira:setup` command isn't recognized, ensure:

1. The package is installed: `composer show akira/setup`
2. Run composer autoload: `composer dump-autoload`
3. The service provider is auto-discovered (check `bootstrap/providers.php`)

### Node Package Manager Not Detected

If the setup can't find your Node package manager:

1. Ensure your package manager is installed: `npm --version`, `yarn --version`, etc.
2. Verify it's in your system PATH
3. Restart your terminal to refresh PATH
4. Manually select your package manager when prompted

### Permission Errors

If you get permission errors during setup:

1. Ensure you have write permissions to your project directory
2. Check that `composer.json` is writable
3. For Node packages, ensure `package.json` is writable

### Setup Fails Partway Through

If the setup process stops with an error:

1. Check that you have sufficient disk space
2. Verify your internet connection
3. Ensure `composer.json` and `package.json` are valid JSON
4. Try running `composer update` before re-running setup

## What Gets Installed

### Composer Packages

The setup installs 2-16+ PHP packages depending on your selections:

- Production packages go to `require`
- Development packages go to `require-dev`
- All packages are compatible with PHP 8.4 and Laravel 12

### Node Packages

If you selected Node packages, they're installed in your `package.json` as dev dependencies:

```json
{
  "devDependencies": {
    "release-it": "^17.x",
    "@commitlint/cli": "^19.x",
    "@commitlint/config-conventional": "^19.x",
    "@release-it/conventional-changelog": "^8.x"
  }
}
```

### Configuration Files

Three configuration files are published to your project root:

- **pint.json** - 140+ code formatting rules for Laravel Pint
- **rector.php** - 50+ PHP refactoring rules for Rector
- **.release-it.json** - Release management configuration

### GitHub Actions

GitHub Actions workflows are installed to:

```
.github/
├── workflows/
│   ├── tests.yml          # Runs tests on PHP 8.4
│   └── release-discord.yml # Notifies Discord on release
├── dependabot.yml         # Automatic dependency updates
├── issue_template/
│   ├── bug.yml            # Bug report template
│   └── feature.yml        # Feature request template
└── FUNDING.yml            # GitHub Sponsors config
```

## Next Steps

After installation, consider:

1. **Review the configuration files** - Customize Pint, Rector, and Release-It to your needs
2. **Run tests** - Execute `composer test` to verify setup
3. **Set up GitHub Actions** - Enable workflows in your repository settings
4. **Configure release automation** - Set up Release-It with GitHub token for automated releases
5. **Add your packages** - Continue developing your Laravel application

## Manual Re-run

You can run the setup command multiple times. It will:

- Re-prompt for all selections
- Update existing configuration files
- Add new packages if you select different ones
- Preserve your existing project code

---

**← Previous:** [Index](./00-index.md) | **Next:** [02 - Architecture Overview →](./02-architecture-overview.md)