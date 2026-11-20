<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Statement;

/**
 * A service class has a `#[Service]` or `#[Route]` attribute, by its short name or a qualified name ending in it.
 */
#[Service]
readonly class IsServiceClassFinder
{
    private const array SERVICE_ATTRIBUTE_NAMES = [
        'Service',
        'Route',
    ];

    public function isServiceClass(Statement $statement): bool
    {
        foreach ($statement->attributes as $open) {
            for ($token = $open->next; $token !== $open->matchingBracket; $token = $token->next) {
                // The attribute names are the class references directly inside #[ ], not those in their arguments
                if ($token->isClassReference
                        && $token->bracketDepth === $open->bracketDepth + 1
                        && in_array($this->shortName($token->text), self::SERVICE_ATTRIBUTE_NAMES, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function shortName(string $name): string
    {
        $position = strrpos($name, '\\');

        return $position === false ? $name : substr($name, $position + 1);
    }
}
