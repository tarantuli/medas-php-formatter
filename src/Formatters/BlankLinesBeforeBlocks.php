<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Tokens\StatementTypes\ControlStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\ReturnStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\ThrowStatement;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(private readonly BlankLineAdder $blankLineAdder)
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
