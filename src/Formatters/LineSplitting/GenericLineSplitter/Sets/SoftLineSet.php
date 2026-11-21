<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Sets;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\{
    BreakpointDefinition,
    BreakpointSet
};
use Medas\PhpTokenizer\TokenType;

class SoftLineSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [
            [
                new BreakpointDefinition(
                    'depth 0 fat arrow',
                    [T_DOUBLE_ARROW],
                    maxDepth: 0,
                    keepPrefixAndSuffix: true
                ),
            ],
            [
                new BreakpointDefinition('depth 0 ternary', [TokenType::QUESTION_MARK, TokenType::COLON], maxDepth: 0),
            ],
            [
                new BreakpointDefinition('depth 0 comma', [TokenType::COMMA], splitAfter: true, maxDepth: 0),
                new BreakpointDefinition(
                    'depth 0 and/or',
                    [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR],
                    maxDepth: 0
                ),
                new BreakpointDefinition(
                    'depth 0 plus/minus/concat',
                    [TokenType::PLUS, TokenType::MINUS, TokenType::CONCATENATOR],
                    maxDepth: 0
                ),
            ],
            [
                new BreakpointDefinition('depth 0 slash', [TokenType::SLASH], maxDepth: 0),
            ],
            [
                new BreakpointDefinition('depth 1 comma', [TokenType::COMMA], splitAfter: true, maxDepth: 1),
            ],
        ];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
