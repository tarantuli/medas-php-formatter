<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;

interface BlockFormatter
{
    public function format(Block $block): void;
}
