<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpFormatter\Tokens\TokenTree;

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
