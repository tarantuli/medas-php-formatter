<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpTokenizer\TokenType;

class Tokens
{
    public const array VARIABLE_STARTERS = [
        ...TokenType::AMPERSANDS,
        T_ELLIPSIS,
        T_VARIABLE,
    ];

    // With the { of "{$a}" and the ${ of "${a}", which are closed by a } too
    public const array OPENING_BRACKETS = [
        TokenType::SQUARE_BRACKET_OPEN,
        TokenType::CURLY_BRACKET_OPEN,
        TokenType::ROUND_BRACKET_OPEN,
        T_CURLY_OPEN,
        T_DOLLAR_OPEN_CURLY_BRACES,
    ];

    public const array CLOSING_BRACKETS = [
        TokenType::SQUARE_BRACKET_CLOSE,
        TokenType::CURLY_BRACKET_CLOSE,
        TokenType::ROUND_BRACKET_CLOSE,
    ];

    public const array CONSTRUCTS = [
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
