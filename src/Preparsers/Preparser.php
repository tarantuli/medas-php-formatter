<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers;

use Medas\PhpTokenizer\TokenCollection;

interface Preparser
{
    public function preparse(TokenCollection $tokens): void;

    public function additionalFormatters(): array;
}
