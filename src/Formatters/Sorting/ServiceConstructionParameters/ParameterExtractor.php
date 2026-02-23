<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Contexts\MethodParameters, Statement};

#[Service]
readonly class ParameterExtractor
{
    /** @return array{Token[][], int|null} */
    public function extract(Statement $statement): array
    {
        $parameters = [];
        $stack = [];
        $firstIndex = null;

        foreach ($statement as $index => $token) {
            if (!$token->context instanceof MethodParameters) {
                continue;
            }

            if ($firstIndex === null) {
                $firstIndex = $index;
            }

            $stack[] = $token;

            if ($token->is(T_COMMA)) {
                $parameters[] = $stack;
                $stack = [];
            }
        }

        if ($stack) {
            $parameters[] = $stack;
        }

        return [$parameters, $firstIndex];
    }
}
