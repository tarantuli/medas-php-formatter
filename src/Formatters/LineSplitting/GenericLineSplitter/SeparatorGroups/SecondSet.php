<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroups;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroup;

class SecondSet implements SeparatorSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best separators on top
        $this->groups = [
            new SeparatorGroup([T_COMMA], true),
            new SeparatorGroup([T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR]),
            new SeparatorGroup([T_QUESTION_MARK, T_COLON]),
            new SeparatorGroup([T_PLUS, T_MINUS]),
        ];
    }

    public function groups(): array
    {
        return $this->groups;
    }
}
