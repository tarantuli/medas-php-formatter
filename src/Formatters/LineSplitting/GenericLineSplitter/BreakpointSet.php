<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

interface BreakpointSet
{
    /** @return BreakpointDefinition[][] */
    public function groupsOfDefinitions(): array;
}
