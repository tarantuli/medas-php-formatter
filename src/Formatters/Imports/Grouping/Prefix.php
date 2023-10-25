<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports\Grouping;

class Prefix
{
    public int $count = 0;
    public int $maxLevel = 0;

    public function __construct(
        public readonly string $prefix,
    )
    {
    }
}
