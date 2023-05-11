<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpTokenizer\TokenTree;

interface Formatter
{
    public function format(TokenTree $tree): void;

    public function additionalFormatters(): array;

    public function priority(): int;
}
