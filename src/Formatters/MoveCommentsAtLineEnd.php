<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

/**
 * This formatter processes the tokens marked by the preparser NoCommentsAtLineEnd. That class also registers this
 * class, so no need to add it manually to Settings.
 */
#[Service]
class MoveCommentsAtLineEnd extends BaseFormatter
{
    public function __construct(private readonly NoCommentsAtLineEnd $preparser)
    {
    }

    public function format(TokenTree $tree): void
    {
        if (!$this->preparser->tokensToMove()) {
            return;
        }

        foreach ($tree as $token) {
            if (in_array($token, $this->preparser->tokensToMove(), true)) {
                // Move this statement before the one before it
                $token->block->moveStatementAfter($token->statement, $token->statement->previous()->previous());
            }
        }
    }
}
