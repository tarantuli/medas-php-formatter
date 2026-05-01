<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

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
        // because a parent split statement (with its own additionalDepth) opened a curly block
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

            // If this statement ends with {, record that statements inside the opened block
            // need the same additionalDepth as this statement
            if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                $additionalByBlockDepth[$depth + 1] = $statement->additionalDepth;
            }
        }
    }
}
