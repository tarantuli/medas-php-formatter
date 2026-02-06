<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;

#[Service]
readonly class isServiceClassFinder
{
    private const array SERVICE_ATTRIBUTE_NAMES = [
        'Service',
        'Route',
    ];

    // TODO: replace naive comparison of the attribute name with a proper AST traversal
    // TODO: also take into account round bracket depth
    public function isServiceClass(mixed $statement): bool
    {
        $inAttribute = false;
        $depth = 0;

        foreach ($statement as $token) {
            if ($token->is(T_ATTRIBUTE)) {
                $inAttribute = true;
                $depth = 0;
            }

            if ($inAttribute
                    && $depth === 0
                    && $token->is(T_STRING)
                    && in_array($token->text, self::SERVICE_ATTRIBUTE_NAMES, true)) {
                return true;
            }

            if ($inAttribute && $token->is(T_SQUARE_BRACKET_OPEN)) {
                ++$depth;
            }

            if ($inAttribute && $token->is(T_SQUARE_BRACKET_CLOSE)) {
                --$depth;

                if ($depth === 0) {
                    $inAttribute = false;
                }
            }
        }

        return false;
    }
}
