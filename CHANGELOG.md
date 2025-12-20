# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).


# [1.3.0](https://github.com/akira-io/setup/compare/1.2.0...1.3.0) (2025-12-20)


### Features

* add illuminate/filesystem dependency and implement basePath method in setup script ([c2bdf47](https://github.com/akira-io/setup/commit/c2bdf4727772b076aa927ca6f609d195d2326d6d))
* add setup script for package installation and update README with usage instructions ([e67900d](https://github.com/akira-io/setup/commit/e67900d04fefa0c78cf746daf6ff445fae01c61d))

# [1.2.0](https://github.com/akira-io/setup/compare/1.1.0...1.2.0) (2025-12-04)


### Bug Fixes

* ensure successful execution of package installation only when required packages are provided ([421d0e7](https://github.com/akira-io/setup/commit/421d0e7967b873d36510515a0b07e718507860e4))


### Features

* enhance package installation feedback and error handling with improved stability checks ([628d4a4](https://github.com/akira-io/setup/commit/628d4a451f94a484c0ad42450ad4848d1f93f5f9))
* implement SkippedPackagesTracker and enhance package installation error handling ([26eb5bc](https://github.com/akira-io/setup/commit/26eb5bc9447e48bde89b2b54e3fa68093c3fc404))
* optimize package installation process by supporting batch installation and improved error handling ([611eeb2](https://github.com/akira-io/setup/commit/611eeb25ad11c24f73dc69b29edefb115d9209e6))
* refactor package installation logic and add HandlesPackageInstallation trait for improved management ([baae11b](https://github.com/akira-io/setup/commit/baae11bc37e7e1e1dfd02ab8b2c243dae0c54263))
* replace info with progress for package installation feedback ([73a9c02](https://github.com/akira-io/setup/commit/73a9c02bc2b3e203369ebadca98b34513334cba6))

# [1.1.0](https://github.com/akira-io/setup/compare/1.0.0...1.1.0) (2025-12-03)


### Features

* add InstallPhpDevPackagesAction and refactor package installation logic ([db3e426](https://github.com/akira-io/setup/commit/db3e426a0c9c3a3e3964a19cceed654ea5e13926))

# 1.0.0 (2025-12-03)


### Features

* add ArchTest for debugging function checks and update development dependencies ([9ae133e](https://github.com/akira-io/setup/commit/9ae133e2efe5123087ad1bfc6c85fa057ac538fd))
* add comprehensive tests for SetupCommand and PackageDetector, including command registration, signature, and dependency checks ([decdf98](https://github.com/akira-io/setup/commit/decdf98aac6f52309095f49efbb7f2d17d6b2348))
* add interactive setup command and configuration files for Laravel project ([ebf0f4c](https://github.com/akira-io/setup/commit/ebf0f4cc2d358517f4aafab87d0fc9d91838150b))
* add issue templates for bug reports and feature requests ([eab9cd3](https://github.com/akira-io/setup/commit/eab9cd37aac90b241df8785f83bb259273287399))
* add tests for PackageDetector to detect various node package managers and handle custom ExecutableFinder ([455ef09](https://github.com/akira-io/setup/commit/455ef09c90edfa70d225e77bfdefb364c5a534e0))
* enhance user choice collection and improve JSON handling in file management ([a81e96f](https://github.com/akira-io/setup/commit/a81e96f0fd31ea77cdc4ae1226cc2301c6cf958d))
* update composer dependencies and improve GitHub Actions workflows ([620a004](https://github.com/akira-io/setup/commit/620a004f864845ef33faf9c257f5dbfa0ef3b675))
* update development dependencies and add PHPStan configuration ([b50d519](https://github.com/akira-io/setup/commit/b50d5193b7d4bcb5766a64e006fda9ba72af382f))
* update Discord release notification configuration and add comprehensive tests for file management and package installation actions ([611056d](https://github.com/akira-io/setup/commit/611056d9075aac446727d0ab68942986df952ac8))
* update test coverage thresholds and improve command signature validation ([0615a4a](https://github.com/akira-io/setup/commit/0615a4a000a17c6905add36872d781d5a6341c9a))
