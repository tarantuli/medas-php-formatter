<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class MaxImportGroupChildDepth implements ConfigOption
{
    public function __construct(
        private PhpFormatterConfigGroup $group,
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
