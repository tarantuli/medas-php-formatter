<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Breakpoints;

interface BreakpointSet
{
    /** @return Definition[][] */
    public function groupsOfGroups(): array;
}
