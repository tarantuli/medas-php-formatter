<?php

declare(strict_types=1);

class ClassElementsSorter
{
    private const METHOD_NAME_PREFIXES = [
        0 => [
            'wijzig' => 6,
            'change' => 6,
            'delete' => 6,
            'remove' => 6,
            'create' => 6,
            'reset' => 5,
            'setIf' => 5,
            'dont' => 4,
            'make' => 4,
            'move' => 4,
            'turn' => 4,
            'set' => 3,
            'add' => 3,
            'do' => 2,
        ],
        1 => [
            'geef' => 4,
            'get' => 3,
            'has' => 3,
            'is' => 2,
        ],
    ];
}
