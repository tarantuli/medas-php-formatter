<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;
use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;

/**
 * This formatter processes the tokens marked by the preparser NoCommentsAtLineEnd. That class also registers this
 * class, so no need to add it manually to Settings.
 */
#[Service]
readonly class MoveCommentsAtLineEnd extends BaseFormatter
{
    public function __construct(
        private NoCommentsAtLineEnd $preparser,
    )
    {
    }

    public function format(Job $job): void
    {
        if (!$this->preparser->tokensToMove()) {
            return;
        }

        foreach ($job->tree as $token) {
            if (in_array($token, $this->preparser->tokensToMove(), true)) {
                // Move this statement before the one before it
                $token->block->moveStatementAfter($token->statement, $token->statement->previous()->previous());
            }
        }
    }
}
