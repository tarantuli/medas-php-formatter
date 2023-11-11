<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroups;

use Medas\Core\AsSingleton;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroup;

class ThirdSet implements SeparatorSet
{
    use AsSingleton;

    private array $groups;

    public function __construct()
    {
        // Sort the best separators on top
        $this->groups = [
            new SeparatorGroup('pipe', [T_PIPE]),
            new SeparatorGroup('arithmetic', [T_PLUS, T_MINUS, T_SLASH]),
            new SeparatorGroup('assignment', [T_ASSIGNMENT], maxDepth: 1),
        ];
    }

    public function groups(): array
    {
        return $this->groups;
    }
}
