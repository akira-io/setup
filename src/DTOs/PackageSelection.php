<?php

declare(strict_types=1);

namespace Akira\Setup\DTOs;

final readonly class PackageSelection
{
    /**
     * @param  array<string>  $phpRequire
     * @param  array<string>  $phpRequireDev
     * @param  array<string>  $nodeDevDependencies
     */
    public function __construct(
        public array $phpRequire,
        public array $phpRequireDev,
        public array $nodeDevDependencies,
        public bool $installWorkflows,
        public string $nodePackageManager,
    ) {}
}
