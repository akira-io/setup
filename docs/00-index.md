# Akira Setup Documentation

Welcome to the Akira Setup comprehensive documentation. This package automates the setup of Laravel projects with
industry best practices, testing tools, code quality standards, and CI/CD workflows.

## Documentation Index

- [01 - Getting Started](./01-getting-started.md) - Installation, basic usage, and running your first setup
- [02 - Architecture Overview](./02-architecture-overview.md) - System design, patterns, and project structure
- [03 - Components Guide](./03-components-guide.md) - Detailed breakdown of all major components
- [04 - Setup Command](./04-setup-command.md) - Complete guide to the `akira:setup` command and workflow
- [05 - Testing Guide](./05-testing-guide.md) - Testing strategy, running tests, and adding new tests
- [06 - Contributing](./06-contributing.md) - How to contribute to the project

## Quick Overview

**Akira Setup** is a Laravel package that streamlines project initialization by:

- Installing curated packages for testing, code quality, and debugging
- Auto-detecting your system configuration (Node.js package managers)
- Publishing pre-configured configuration files (Pint, Rector, Release-It)
- Installing GitHub Actions CI/CD workflows and templates
- Adding convenient composer and npm scripts for common development tasks

### Key Features

- **Interactive Setup** - User-friendly prompts guide you through package selection
- **Smart Defaults** - Recommended packages are pre-selected, override as needed
- **System Detection** - Automatically finds npm, pnpm, yarn, or bun on your system
- **Configuration Publishing** - Ships with best-practice configuration files
- **GitHub Actions** - Ready-to-use CI/CD workflows and issue templates
- **Script Aliases** - Convenient `composer test`, `composer lint` commands
- **Type-Safe** - PHP 8.4 with strict types and full type hints throughout

### Getting Help

- Start with [Getting Started](./01-getting-started.md) for installation and basic usage
- Check [Architecture Overview](./02-architecture-overview.md) to understand the project structure
- Read [Components Guide](./03-components-guide.md) for detailed component documentation
- See [Testing Guide](./05-testing-guide.md) to understand and run tests
- Visit [Contributing](./06-contributing.md) if you want to contribute

---

**Next:** [01 - Getting Started →](./01-getting-started.md)