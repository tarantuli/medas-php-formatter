<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\TokenTree;

interface Formatter
{
    public function format(TokenTree $tree): void;

    public function additionalFormatters(): array;

    public function priority(): int;
}
