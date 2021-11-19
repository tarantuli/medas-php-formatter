<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\TokenFormatters;

use Medas\PhpBeautifier\Tokens\TokenCollection;

interface TokenFormatter
{
    public function format(TokenCollection $tokens): void;
}
