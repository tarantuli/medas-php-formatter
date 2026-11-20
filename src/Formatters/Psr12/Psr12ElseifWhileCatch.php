<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\Statement;

/**
 * Puts `else`, `elseif`, `catch`, `finally` and the `while` of a do-while loop on the same line as the preceding `}`.
 */
#[Service]
readonly class Psr12ElseifWhileCatch extends BaseFormatter
{
    public function priority(): int
    {
        return 1400;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->statements() as $statement) {
            $first = $statement->firstToken();

            // An else: or elseif (...): of alternative syntax closes the block before it, and stays on its own line
            if ($first->is([T_ELSE, T_ELSEIF, T_CATCH, T_FINALLY]) && !$first->closesBlock) {
                $statement->mergeWithPrevious();
            }

            if ($this->isDoWhileCondition($statement)) {
                $statement->mergeWithPrevious();
            }
        }
    }

    /**
     * A `while (…);` directly after the `}` of a `do` block. An empty `while` loop also ends with `;`, but it follows
     * something else.
     */
    private function isDoWhileCondition(Statement $statement): bool
    {
        if (!$statement->firstToken()->is(T_WHILE) || !$statement->lastToken()->is(T_SEMICOLON)) {
            return false;
        }

        $closer = $statement->previous();

        if ($closer === null || $closer->tokenCount() !== 1 || !$closer->firstToken()->is(T_CURLY_BRACKET_CLOSE)) {
            return false;
        }

        // The last statement inside the closed block tells which statement opened that block
        $opener = $closer->previous()?->block->opener;

        return $opener?->firstNonCommentToken()?->is(T_DO) ?? false;
    }
}
