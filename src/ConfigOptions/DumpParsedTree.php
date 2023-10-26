<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};

#[Service]
readonly class DumpParsedTree implements ConfigOption
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
        return 'dump-parsed-tree';
    }

    public function description(): string
    {
        return 'Whether to dump the parsed token tree to output before processing it';
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
