<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Statement, Token, TokenType};

#[Service]
readonly class ParameterList
{
    /**
     * The "(" after the name of the function that the statement declares. It skips the parentheses of attributes
     * before the declaration. Its `matchingBracket` closes the list.
     */
    public function opener(Statement $statement): Token|null
    {
        $keyword = $statement->declaration?->keyword;

        for ($token = $keyword?->next; $token?->statement === $statement; $token = $token->next) {
            if ($token->is(TokenType::ROUND_BRACKET_OPEN)) {
                return $token;
            }
        }

        return null;
    }
}
