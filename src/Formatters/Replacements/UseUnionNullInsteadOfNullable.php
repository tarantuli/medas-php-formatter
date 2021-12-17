<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Replacements;

use Medas\PhpBeautifier\Formatters\BaseFormatter;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

/**
 * Use "type|null" instead of "?type" in property, parameter and return type definitions
 */
#[Service]
class UseUnionNullInsteadOfNullable extends BaseFormatter
{
    public function format(TokenTree $tree): void
    {
        // TODO: Implement format() method.
    }
}
