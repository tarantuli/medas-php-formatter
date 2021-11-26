<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Replacements;

use Medas\PhpBeautifier\Formatters\Formatter;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

/**
 * Use "type|null" instead of "?type" in property, parameter and return type definitions
 */
#[Service]
class UseUnionNullInsteadOfNullable implements Formatter
{
    public function format(TokenCollection $tokens): void
    {
        // TODO: Implement format() method.
    }
}
