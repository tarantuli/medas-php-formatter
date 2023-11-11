<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class DumpOptionAssessment implements ConfigOption
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
        return 'dump-option-assessment';
    }

    public function description(): string
    {
        return 'Whether to dump the breakpoint options and quality before selecting the best one';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): false
    {
        return false;
    }
}
