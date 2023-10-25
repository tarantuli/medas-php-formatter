<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpTokenizer\TokenTree;

interface Formatter
{
    public function format(TokenTree $tree): void;

    public function additionalFormatters(): array;

    /**
     * Formatters with higher priority values are called first, lower values are called later.
     */
    public function priority(): int;
}
