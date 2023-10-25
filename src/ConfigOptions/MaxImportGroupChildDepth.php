<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ConfigGroup;
use Medas\Core\Interfaces\ConfigOption;

#[Service]
readonly class MaxImportGroupChildDepth implements ConfigOption
{
    public function __construct(
        private RootGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'max-import-group-child-depth';
    }

    public function description(): string
    {
        return 'The maximum amount of levels a grouped import clause may have (e.g. two in "use Namespace\{SubA\B, SubC\D};")';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        return 2;
    }
}
