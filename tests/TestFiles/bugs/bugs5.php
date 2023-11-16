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

    /**
     * @var  array[]
     */
    private static $INTERFACE_IMPLEMENTATIONS = [
        \Iterator::class => ['current', 'next', 'key', 'valid', 'rewind'],
        \Countable::class => ['count']
    ];

    /**
     * @var  array[]
     */
    protected $BlockTypes = [
        '#' => ['Header'],
        '*' => ['Rule', 'List'],
        '+' => ['List'],
        '-' => ['SetextHeader', 'Table', 'Rule', 'List'],
        '0' => ['List'],
        '1' => ['List'],
        '2' => ['List'],
        '3' => ['List'],
        '4' => ['List'],
        '5' => ['List'],
        '6' => ['List'],
        '7' => ['List'],
        '8' => ['List'],
        '9' => ['List'],
        ':' => ['Table'],
        '<' => ['Comment', 'Markup'],
        '=' => ['SetextHeader'],
        '>' => ['Quote'],
        '[' => ['Reference'],
        '_' => ['Rule'],
        '`' => ['FencedCode'],
        '|' => ['Table'],
        '~' => ['FencedCode'],
    ];
}
