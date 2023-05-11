<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\StatementTypes\{ClassConstDeclaration, ClassPropertyDeclaration, Comment, UseTraitStatement};
use Medas\PhpTokenizer\TokenTree;

#[Service]
class BlankLinesBetweenClassSections extends BaseFormatter
{
    public function __construct(
        private readonly BlankLineAdder $blankLineAdder,
    )
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
