<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

class Option
{
    public int $counter = 0;

    /** @var int[] */
    public array $separatorIndices = [];
    public int|null $closerIndex = null;

    public function __construct(
        public readonly SeparatorGroup $group,
        public readonly int            $depth,
        public readonly int            $cluster,
        public readonly int            $openerIndex,
    )
    {
    }
}
