<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

/**
 * This formatter processes the tokens marked by NoCommentsAtLineEnd. That class also registers this class, so
 * no need to add it manually to Settings.
 */
#[Service]
class MoveCommentsAtLineEnd extends BaseFormatter
{
    public function __construct(private NoCommentsAtLineEnd $marker)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        if (!$this->marker->tokensToMove) {
            return;
        }

        foreach ($tokens as $token) {
            if (in_array($token, $this->marker->tokensToMove, true)) {
                // Move this statement before the one before it
                $token->block->moveStatementAfter($token->statement->previous()->previous(), $token->statement);
            }
        }
    }
}
