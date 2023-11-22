<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Breakpoints\Definition;

class Option
{
    public int $counter = 0;

    /** @var int[] */
    public array $breakpointIndices = [];

    public function __construct(
        public readonly Definition $breakpointDefinition,
        public readonly int        $depth,
        public readonly int        $cluster,
        public readonly int        $openerIndex,
        public int                 $closerIndex,
    )
    {
    }
}
