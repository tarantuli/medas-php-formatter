<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Sets;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\{
    BreakpointDefinition,
    BreakpointSet
};

class ControlStatementSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [[
            new BreakpointDefinition(
                'and/or',
                [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR],
                keepPrefixAndSuffix: true,
                additionalDepth: 2
            ),
            new BreakpointDefinition('comma', [T_COMMA], splitAfter: true),
        ]];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
