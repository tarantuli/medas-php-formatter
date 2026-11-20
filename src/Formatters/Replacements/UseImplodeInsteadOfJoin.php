<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Token, TokenType};

/**
 * Replaces calls to the global `join()` function by `implode()`.
 *
 * @notFor Methods, static methods or function declarations named `join`; those are left alone.
 */
#[Service]
readonly class UseImplodeInsteadOfJoin extends BaseTokenReplacer
{
    /**
     * After these tokens, `join` is a method, a static method or a declaration rather than a call to the global
     * function.
     */
    private const array NON_FUNCTION_CALL_PREFIXES = [
        T_OBJECT_OPERATOR,
        T_NULLSAFE_OBJECT_OPERATOR,
        T_DOUBLE_COLON,
        T_FUNCTION,
        T_CONST,
    ];

    protected function doReplace(Token $token): bool
    {
        // A class named Join, as in "new Join()", is no function call
        if (!$token->next?->is(TokenType::ROUND_BRACKET_OPEN) || $token->isClassReference) {
            return false;
        }

        if ($token->is(T_NAME_FULLY_QUALIFIED)) {
            return strtolower($token->text) === '\\join';
        }

        return $token->is(T_STRING)
            && strtolower($token->text) === 'join'
            && !$token->previous?->is(self::NON_FUNCTION_CALL_PREFIXES);
    }

    protected function update(Token $token): void
    {
        $token->text = $token->is(T_NAME_FULLY_QUALIFIED) ? '\\implode' : 'implode';
    }
}
