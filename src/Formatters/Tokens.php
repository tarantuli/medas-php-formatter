<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

class Tokens
{
    public const VARIABLE_STARTERS = [
        T_AMPERSAND,
        T_ELLIPSIS,
        T_VARIABLE,
    ];

    public const OPENING_BRACKETS = [
        T_SQUARE_BRACKET_CLOSE => T_SQUARE_BRACKET_OPEN,
        T_CURLY_BRACKET_CLOSE => T_CURLY_BRACKET_OPEN,
        T_ROUND_BRACKET_CLOSE => T_ROUND_BRACKET_OPEN,
    ];

    public const CLOSING_BRACKETS = [
        T_SQUARE_BRACKET_CLOSE,
        T_CURLY_BRACKET_CLOSE,
        T_ROUND_BRACKET_CLOSE,
    ];

    public const CONSTRUCTS = [
        T_BREAK,
        T_CONTINUE,
        T_ECHO,
        T_EXIT,
        T_GLOBAL,
        T_GOTO,
        T_INCLUDE,
        T_INCLUDE_ONCE,
        T_REQUIRE,
        T_REQUIRE_ONCE,
        T_RETURN,
        T_YIELD,
        T_YIELD_FROM,
    ];
}
