<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Preparsers;

use Medas\PhpBeautifier\Tokens\TokenCollection;

interface Preparser
{
    public function preparse(TokenCollection $tokens): void;

    public function additionalFormatters(): array;
}
