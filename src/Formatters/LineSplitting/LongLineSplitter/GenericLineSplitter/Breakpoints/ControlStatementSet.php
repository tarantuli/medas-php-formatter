<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Breakpoints;

use Medas\Core\AsSingleton;

class ControlStatementSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [[
            new Definition('and/or', [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR], keepPrefixAndSuffix: true, additionalDepth: 2),
            new Definition('comma', [T_COMMA], splitAfter: true),
        ]];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
