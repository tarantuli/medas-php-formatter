<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Token, TokenRole};

/**
 * Use "type|null" instead of "?type" in property, parameter, and return type definitions
 */
#[Service]
readonly class UseUnionNullInsteadOfNullable extends BaseFormatter
{
    public function priority(): int
    {
        return 2200;
    }

    public function format(Job $job): void
    {
        foreach ($job->tokens as $token) {
            if ($token->role === TokenRole::NullableMarker
                    && $token->next?->is([T_STRING, T_ARRAY, T_CALLABLE, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                $this->replaceByUnionNull($token);
            }
        }
    }

    private function replaceByUnionNull(Token $token): void
    {
        $statement = $token->statement;

        $barToken = $this->typeToken('|', $token);
        $nullToken = $this->typeToken('null', $token);

        $statement->insertTokenAfter($barToken, $token->next);
        $statement->insertTokenAfter($nullToken, $barToken);
        $statement->removeToken($token);
    }

    /**
     * A token of the same type declaration as the "?" it replaces.
     */
    private function typeToken(string $text, Token $questionMark): Token
    {
        $token = Token::fromText($text, $questionMark);

        $token->typeDeclaration = $questionMark->typeDeclaration;
        $token->bracketDepth = $questionMark->bracketDepth;

        return $token;
    }
}
