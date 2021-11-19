<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassConstDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\Comment;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ClassPartsBlankLines implements BlockFormatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(Block $block): void
    {
        $this->blankLineAdder->afterTypes($block, [
            ClassConstDeclaration::class,
            ClassPropertyDeclaration::class,
        ]);

        $this->blankLineAdder->beforeTypes($block, [
            Comment::class,
        ]);
    }
}
