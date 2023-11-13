<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroups;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroup;

class FirstSet implements SeparatorSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best separators on top
        $this->groups = [
            new SeparatorGroup('ternary', [T_QUESTION_MARK, T_COLON], maxDepth: 0),
            new SeparatorGroup('comma', [T_COMMA], splitAfter: true, maxDepth: 0),
            new SeparatorGroup('comma', [T_COMMA], splitAfter: true, maxDepth: 1),
            new SeparatorGroup('and/or', [T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR], maxDepth: 0),
            new SeparatorGroup('arithmetic', [T_PLUS, T_MINUS], maxDepth: 0),
        ];
    }

    public function groups(): array
    {
        return $this->groups;
    }
}
