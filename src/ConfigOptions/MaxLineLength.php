<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class MaxLineLength implements ConfigOption
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
        return 'max-line-length';
    }

    public function description(): string
    {
        return 'The maximum length of lines; LongLineSplitter will attempt to split lines longer than this over multiple lines';
    }

    public function hasDefault(): bool
    {
        return true;
    }

    public function default(): int
    {
        return 120;
    }
}
