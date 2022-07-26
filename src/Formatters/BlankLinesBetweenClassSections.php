<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Tokens\StatementTypes\ClassConstDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\Comment;
use Medas\PhpFormatter\Tokens\StatementTypes\UseTraitStatement;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBetweenClassSections extends BaseFormatter
{
    public function __construct(private readonly BlankLineAdder $blankLineAdder)
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
