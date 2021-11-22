<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\StatementTypes\ControlStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBeforeBlocks implements BlockFormatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(Block $block): void
    {
        $this->blankLineAdder->beforeTypes($block, [
            ControlStatement::class,
        ]);
    }
}
