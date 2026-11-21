<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Token, TokenGroups, TokenType};

#[Service]
readonly class ParameterComparer
{
    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
    }

    /**
     * @param Token[] $a
     * @param Token[] $b
     */
    public function compare(array $a, array $b): int
    {
        /**
         * - No default value > Default value
         * - No visibility markers > public > protected > private
         * - Alphabetically
         */
        // No default value > Default value
        $diff = $this->hasDefault($a) - $this->hasDefault($b);

        if ($diff) {
            return $diff;
        }

        // No visibility markers > public > protected > private
        $diff = $this->visibility($a) - $this->visibility($b);

        if ($diff) {
            return $diff;
        }

        return strcmp($this->type($a), $this->type($b));
    }

    /** @param Token[] $tokens */
    private function hasDefault(array $tokens): int
    {
        if (array_any($tokens, fn($token) => $token->is(TokenType::ASSIGNMENT))) {
            return 1;
        }

        return 0;
    }

    /** @param Token[] $tokens */
    private function visibility(array $tokens): int
    {
        foreach ($tokens as $token) {
            if ($token->is(T_PRIVATE)) {
                return 0;
            }

            if ($token->is(T_PROTECTED)) {
                return 1;
            }

            if ($token->is(T_PUBLIC)) {
                return 2;
            }
        }

        return 3;
    }

    /** @param Token[] $tokens */
    private function type(array $tokens): string
    {
        foreach ($tokens as $token) {
            if ($token->typeDeclaration !== null && $token->is($this->tokenGroups->typeDeclarationTypes())) {
                return $token->text;
            }
        }

        return '';
    }
}
