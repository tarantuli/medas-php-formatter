<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Token;

#[Service]
readonly class ParameterComparer
{
    /**
     * @param Token[] $a
     * @param Token[] $b
     *
     * TODO implement third sorting type (attributes)
     */
    public function compare(array $a, array $b): int
    {
        /**
         * - No default value > Default value
         * - No visibility markers > public > protected > private
         * - With attributes > Without attributes
         * - Alphabetically
         */
        // No default value > Default value
        $diff = $this->hasDefault($a) - $this->hasDefault($b);

        if ($diff) {
            return -$diff;
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
        if (array_any($tokens, fn($token) => $token->is(T_IS_EQUAL))) {
            return 1;
        }

        return 0;
    }

    /** @param Token[] $tokens */
    private function visibility(array $tokens): int
    {
        foreach ($tokens as $token) {
            if ($token->is(T_PRIVATE)) {
                return 3;
            }

            if ($token->is(T_PROTECTED)) {
                return 2;
            }

            if ($token->is(T_PUBLIC)) {
                return 1;
            }
        }

        return 0;
    }

    /** @param Token[] $tokens */
    private function type(array $tokens): string
    {
        foreach ($tokens as $token) {
            if ($token->inTypeDeclaration) {
                return $token->text;
            }
        }

        return '';
    }
}
