<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\{Contexts\MethodParameters, Statement, Token};

#[Service]
readonly class ParameterExtractor
{
    /** @return array{Token[][], int|null} */
    public function extract(Statement $statement): array
    {
        $parameters = [];
        $stack = [];
        $depth = 0;
        $firstIndex = null;

        foreach ($statement as $index => $token) {
            if (!$token->context instanceof MethodParameters) {
                continue;
            }

            if ($token->is(Tokens::OPENING_BRACKETS)) {
                ++$depth;
            }

            if ($firstIndex === null) {
                $firstIndex = $index;
            }

            $stack[] = $token;

            if ($depth === 0 && $token->is(T_COMMA)) {
                $parameters[] = $stack;
                $stack = [];
            }

            if ($token->is(Tokens::CLOSING_BRACKETS)) {
                --$depth;
            }
        }

        if ($stack) {
            $parameters[] = $stack;
        }

        return [$parameters, $firstIndex];
    }
}
