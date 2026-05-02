<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class CurlyBlockDepthPropagator extends BaseFormatter
{
    public function priority(): int
    {
        return 480;
    }

    public function format(Job $job): void
    {
        $tree = $job->tree;

        // Maps block->depth to the additionalDepth that should be added to statements at that depth,
        // because a parent lambda/closure statement opened a curly block while itself being shifted
        // as an argument inside a split call.
        $additionalByBlockDepth = [];

        foreach ($tree->statements() as $statement) {
            $depth = $statement->block->depth;

            // Apply propagated depth for this block level
            $statement->additionalDepth += $additionalByBlockDepth[$depth] ?? 0;

            // If this statement starts with }, it closes the block at depth+1.
            // Apply the difference so the } aligns with the statement that opened the block,
            // then clear the tracking for the closed depth.
            if ($statement->firstToken()->is(T_CURLY_BRACKET_CLOSE) && isset($additionalByBlockDepth[$depth + 1])) {
                $statement->additionalDepth += $additionalByBlockDepth[$depth + 1]
                    - ($additionalByBlockDepth[$depth] ?? 0);

                unset($additionalByBlockDepth[$depth + 1]);
            }

            // If this statement ends with {, determine how much depth to propagate into the
            // child block.
            if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                if ($this->shouldPropagateOwnDepth($statement)) {
                    // Lambda/closure or property hook opener: the child block inherits the full
                    // additionalDepth of this statement, because the entire statement was shifted
                    // as an argument inside a split call or promoted hook declaration.
                    $additionalByBlockDepth[$depth + 1] = $statement->additionalDepth;
                }
                else {
                    // Control structure, method, class etc.: only pass through the propagation
                    // that was inherited from the parent block.
                    $additionalByBlockDepth[$depth + 1] = $additionalByBlockDepth[$depth] ?? 0;
                }
            }
        }
    }

    private function shouldPropagateOwnDepth(Statement $statement): bool
    {
        $hasVariable = false;
        $hasRoundBracketClose = false;

        foreach ($statement as $token) {
            if ($token->is([T_FUNCTION, T_CLASS])) {
                // Lambda or anonymous class: propagate own depth.
                return true;
            }

            if ($token->is(T_VARIABLE)) {
                $hasVariable = true;
            }

            if ($token->is(T_ROUND_BRACKET_CLOSE)) {
                $hasRoundBracketClose = true;
            }
        }

        // Property hook opener: ends with {, contains a variable but no ) before {.
        // Control structures (while, if, foreach) always have ) before {, so they are
        // correctly excluded. Regular method declarations have ) before { too.
        return $hasVariable && !$hasRoundBracketClose;
    }
}
