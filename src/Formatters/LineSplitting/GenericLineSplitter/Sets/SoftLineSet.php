<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Sets;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\{
    BreakpointDefinition,
    BreakpointSet
};

class SoftLineSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [
            [
                new BreakpointDefinition('depth 0 ternary', [T_QUESTION_MARK, T_COLON], maxDepth: 0),
            ],
            [
                new BreakpointDefinition('depth 0 comma', [T_COMMA], splitAfter: true, maxDepth: 0),
                new BreakpointDefinition(
                    'depth 0 and/or',
                    [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR],
                    maxDepth: 0
                ),
                new BreakpointDefinition(
                    'depth 0 plus/minus/concat',
                    [T_PLUS, T_MINUS, T_CONCATENATOR],
                    maxDepth: 0
                ),
            ],
            [
                new BreakpointDefinition('depth 0 slash', [T_SLASH], maxDepth: 0),
            ],
            [
                new BreakpointDefinition('depth 1 comma', [T_COMMA], splitAfter: true, maxDepth: 1),
            ],
        ];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
