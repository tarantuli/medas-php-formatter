<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\StatementTypes\ClassConstDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\Comment;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseTraitStatement;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBetweenClassSections extends BaseFormatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(TokenTree $tree): void
    {
        $this->blankLineAdder->afterTypes($tree, [
            UseTraitStatement::class,
            ClassConstDeclaration::class,
            ClassPropertyDeclaration::class,
        ]);

        $this->blankLineAdder->beforeTypes($tree, [
            Comment::class,
        ]);
    }
}
