<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports\Grouping;

class Prefix
{
    public int $count = 0;
    public int $maxChildDepth = 0;
    public int $prefixDepth;

    public function __construct(
        public readonly string $prefix,
    )
    {
        $this->prefixDepth = substr_count($this->prefix, '\\');
    }
}
