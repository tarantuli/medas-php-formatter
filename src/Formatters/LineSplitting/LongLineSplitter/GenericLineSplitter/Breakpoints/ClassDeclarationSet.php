<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Breakpoints;

use Medas\Core\AsSingleton;

class ClassDeclarationSet implements BreakpointSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best breakpoints on top
        $this->groups = [[
            new Definition('implements/extends', [T_IMPLEMENTS, T_EXTENDS], keepPrefixAndSuffix: true, additionalDepth: 1),
        ]];
    }

    public function groupsOfDefinitions(): array
    {
        return $this->groups;
    }
}
