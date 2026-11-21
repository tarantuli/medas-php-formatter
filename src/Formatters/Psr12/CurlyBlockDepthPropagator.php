<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\BlockType;

/**
 * A closure, anonymous class, match or hook list that is shifted along with a split call or a promoted parameter takes
 * its body with it: the statements in its block get the same additional depth.
 */
#[Service]
readonly class CurlyBlockDepthPropagator extends BaseFormatter
{
    /**
     * The blocks whose statements are indented relative to the statement that opens them, shifted or not. These are
     * the ones that can be part of an expression, like an argument. The body of a control structure, switch etc. only
     * passes on what its own block got.
     */
    private const array OWN_DEPTH_BLOCKS = [
        BlockType::Closure,
        BlockType::ClassLike,
        BlockType::Function,
        BlockType::HookList,
        BlockType::Match,
    ];

    public function priority(): int
    {
        return 480;
    }

    public function format(Job $job): void
    {
        // The additional depth for the statements in a block, by block depth
        $additionalByBlockDepth = [];

        foreach ($job->tree->statements() as $statement) {
            $depth = $statement->block->depth;
            $statement->additionalDepth += $additionalByBlockDepth[$depth] ?? 0;

            // A closer, like "}" or "endif", lines up with the statement that opened its block
            if ($statement->firstToken()?->closesBlock && isset($additionalByBlockDepth[$depth + 1])) {
                $statement->additionalDepth += $additionalByBlockDepth[$depth + 1]
                    - ($additionalByBlockDepth[$depth] ?? 0);

                unset($additionalByBlockDepth[$depth + 1]);
            }

            $opens = $statement->lastToken()?->opensBlock;

            if ($opens !== null) {
                $additionalByBlockDepth[$depth + 1] = in_array($opens, self::OWN_DEPTH_BLOCKS, true)
                    ? $statement->additionalDepth
                    : $additionalByBlockDepth[$depth] ?? 0;
            }
        }
    }
}
