<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Reformatters;

use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequiredWhitespaceAdder
{
    private const NO_SPACE_AFTER = [
        T_AMPERSAND,
        T_ATTRIBUTE,
        T_CURLY_BRACKET_OPEN,
        T_CURLY_OPEN,
        T_DOLLAR,
        T_DOUBLE_COLON,
        T_DOUBLE_QUOTE,
        T_ENCAPSED_AND_WHITESPACE,
        T_EXCLAMATION_POINT,
        T_INLINE_HTML,
        T_NS_SEPARATOR,
        T_OBJECT_OPERATOR,
        T_OPEN_TAG_WITH_ECHO,
        T_ROUND_BRACKET_OPEN,
        T_SQUARE_BRACKET_OPEN,
        T_WHITESPACE,
    ];

    private const NO_SPACE_BEFORE = [
        T_ROUND_BRACKET_OPEN,
        T_ROUND_BRACKET_CLOSE,
        T_SEMICOLON,
        T_SQUARE_BRACKET_CLOSE,
        T_OBJECT_OPERATOR,
        T_COMMA,
        T_DOUBLE_COLON,
        T_COLON,
    ];

    public function add(TokenCollection $tokens): void
    {
        foreach ($tokens as $token) {
            $addSpaceAfter = true;

            if ($token->inString) {
                $addSpaceAfter = false;
            }
            elseif ($token->is(self::NO_SPACE_AFTER)) {
                $addSpaceAfter = false;
            }
            elseif ($token->statement->getLastToken() === $token) {
                $addSpaceAfter = false;
            }

            elseif ($token->statement->getTokenAfter($token)->is(self::NO_SPACE_BEFORE)) {
                $addSpaceAfter = false;
            }


            if ($addSpaceAfter) {
                $token->statement->insertTokenAfter($token, Token::space());
            }
        }
    }
}
