<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Breakpoints;

class Option
{
    public int $counter = 0;

    /** @var int[] */
    public array $breakpointIndices = [];

    public int|null $closerIndex = null;

    public function __construct(
        public readonly Breakpoints\Definition $breakpointDefinition,
        public readonly int                    $depth,
        public readonly int                    $cluster,
        public readonly int                    $openerIndex,
    )
    {
    }
}
