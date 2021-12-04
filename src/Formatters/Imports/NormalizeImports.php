<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Imports;

use Medas\PhpBeautifier\Formatters\BaseFormatter;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class NormalizeImports extends BaseFormatter
{
    public function format(TokenCollection $tokens): void
    {
        // TODO: Implement format() method.
    }
}
