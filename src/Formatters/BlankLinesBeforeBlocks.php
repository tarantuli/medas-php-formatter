<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\StatementTypes\{ControlStatement, ReturnStatement, ThrowStatement};
use Medas\PhpTokenizer\TokenTree;

#[Service]
class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(
        private readonly BlankLineAdder $blankLineAdder,
    )
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
