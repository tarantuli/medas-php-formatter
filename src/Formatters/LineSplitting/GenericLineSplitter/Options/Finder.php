<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\BreakpointDefinition;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\Statement;

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
