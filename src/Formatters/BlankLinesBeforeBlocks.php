<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\StatementTypes\ControlStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\ReturnStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\ThrowStatement;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(private BlankLineAdder $blankLineAdder)
    {
    }

    public function format(TokenTree $tree): void
    {
        $this->blankLineAdder->beforeTypes($tree, [
            ControlStatement::class,
            ReturnStatement::class,
            ThrowStatement::class,
        ]);
    }
}
