<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Sets;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\{
    BreakpointDefinition,
    BreakpointSet
};

class ClassDeclarationSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [[
            new BreakpointDefinition(
                'implements/extends',
                [T_IMPLEMENTS, T_EXTENDS],
                keepPrefixAndSuffix: true,
                additionalDepth: 1
            ),
        ]];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
