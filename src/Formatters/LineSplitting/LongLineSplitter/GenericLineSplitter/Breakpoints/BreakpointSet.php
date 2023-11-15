<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Breakpoints;

interface BreakpointSet
{
    /** @return Definition[][] */
    public function groupsOfDefinitions(): array;
}
