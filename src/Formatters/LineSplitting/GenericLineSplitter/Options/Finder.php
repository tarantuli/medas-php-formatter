<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointDefinition;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\Statement;

/**
 * Finds candidate split points (Options) within a statement for a given BreakpointDefinition.
 *
 * For each definition, the finder scans the statement's tokens and records every token index
 * that matches the definition's separators (e.g., commas, object operators). Each candidate
 * is grouped into a Cluster by bracket depth, so splits at different nesting levels are
 * assessed independently. The result is a list of Options, each describing a possible split:
 * which tokens are the breakpoints, what the opener and closer indices are, and how deep
 * the split sits in the bracket hierarchy.
 *
 * Specifics:
 *
 * - Bracket depth tracking: every opening bracket increments the depth and starts a new
 *   Cluster; every closing bracket decrements it and returns to the parent Cluster. This
 *   means separators inside nested brackets are considered as separate, independent options
 *   from separators at the outer level.
 *
 * - Match branch handling: when the statement is a match branch (contains => and lives
 *   inside a match block), all tokens before the => are skipped. This prevents the finder
 *   from proposing a split inside the match condition; only the value expression (after =>)
 *   is eligible for splitting.
 *
 * - Quote tracking: separators inside double-quoted strings are ignored.
 *
 * - Named argument colons and ternary ?-: pairs are excluded as separator candidates even
 *   when T_COLON is listed as a separator in the definition.
 *
 * - Options that contain no real breakpoints (opener+1 >= closer) are removed in cleanup,
 *   as are options that exceed the definition's maxDepth.
 */
#[Service]
readonly class Finder
{
    /** @return Option[] */
    public function find(Statement $statement, BreakpointDefinition $definition): array
    {
        /** @var Option[][] $options */
        $options = [];
        $clusterManager = new ClusterManager();
        $cluster = $clusterManager->currentCluster();
        $depth = 0;
        $tokenCount = $statement->tokenCount();
        $lastToken = $statement->lastToken();

        $isMatchBranch = $statement->containsType(T_DOUBLE_ARROW)
            && $statement->block->opener->containsType(T_MATCH);

        $foundDoubleArrow = false;
        $inQuote = false;

        foreach ($statement as $index => $token) {
            if ($token === $lastToken) {
                continue;
            }

            if ($token->is(Tokens::OPENING_BRACKETS)) {
                ++$depth;

                $cluster = $clusterManager->getNextCluster($depth, $index);
            }

            if ($isMatchBranch && !$foundDoubleArrow) {
                if ($token->is(T_DOUBLE_ARROW)) {
                    $foundDoubleArrow = true;
                }
                else {
                    continue;
                }
            }

            $questionPlusColonCheck = $token->is(T_COLON)
                && $token->previous
                && $token->previous->is(T_QUESTION_MARK);

            $argumentNameCheck = $token->is(T_COLON)
                && $token->previous
                && $token->previous->is(T_STRING);

            if ($token->is(T_DOUBLE_QUOTE)) {
                $inQuote = !$inQuote;
            }

            if (!$inQuote && !$questionPlusColonCheck && !$argumentNameCheck && $token->is($definition->separators)) {
                if (!array_key_exists($cluster->id, $options)) {
                    $openerIndex = $cluster->openerIndex;

                    if ($definition->keepPrefixAndSuffix) {
                        $openerIndex = $definition->splitAfter ? $index + 1 : $index - 1;
                    }

                    if ($openerIndex < 0) {
                        $openerIndex = $definition->splitAfter ? $index : $index - 1;
                    }

                    $options[$cluster->id] = new Option(
                        $definition,
                        $depth,
                        $cluster->id,
                        $openerIndex,
                        $tokenCount
                    );
                }

                ++$options[$cluster->id]->counter;

                $options[$cluster->id]->breakpointIndices[] = $index;
            }

            if ($token->is(Tokens::CLOSING_BRACKETS) && $depth > 0) {
                if (isset($options[$cluster->id]) && !$definition->keepPrefixAndSuffix) {
                    $options[$cluster->id]->closerIndex = $index;
                }

                --$depth;

                $cluster = $clusterManager->getPreviousCluster($depth);
            }
        }

        return $this->cleanUpOptions($options, $definition);
    }

    private function cleanUpOptions(array $options, BreakpointDefinition $definition): array
    {
        /** @var Option[] $options */
        $options = $this->flatten($options);

        // Remove options that are too deeply nested
        if ($definition->maxDepth !== null) {
            foreach ($options as $i => $option) {
                if ($option->depth > $definition->maxDepth) {
                    unset($options[$i]);
                }
            }
        }

        // Remove options that don't do anything
        foreach ($options as $i => $option) {
            if ($option->openerIndex + 1 >= $option->closerIndex) {
                unset($options[$i]);
            }
        }

        return $options;
    }

    private function flatten(array $array): array
    {
        $return = [];

        array_walk_recursive($array, function (Option $a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }
}
