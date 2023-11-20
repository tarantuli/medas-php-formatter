<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Breakpoints;

readonly class Definition
{
    public function __construct(
        public string   $name,
        public array    $separators,
        public bool     $splitAfter = false,
        public int|null $maxDepth = null,
        public bool     $keepPrefixAndSuffix = false,
        public int      $additionalDepth = 1,
    )
    {
    }
}
