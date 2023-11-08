<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

readonly class SeparatorGroup
{
    public function __construct(
        public array $separators,
        public bool  $splitAfter = false,
    )
    {
    }
}
