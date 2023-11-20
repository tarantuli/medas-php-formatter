<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\{Breakpoints\Definition,
    ClusterManager
};
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class Finder
{
    /** @return Option[] */
    public function find(Statement $statement, Definition $definition): array
    {
        /** @var Option[][] $options */
        $options = [];
        $clusterManager = new ClusterManager();
        $cluster = $clusterManager->currentCluster();
        $depth = 0;
        $tokenCount = $statement->tokenCount();
        $lastToken = $statement->lastToken();

        $isMatchBranch = $statement->containsType(T_DOUBLE_ARROW) && $statement->block->opener->containsType(T_MATCH);
        $foundDoubleArrow = false;

        foreach ($statement as $index => $token) {
            if ($token === $lastToken) {
                continue;
            }

            if ($token->is(TrailingCommaSplitter::BRACKETS)) {
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

            $questionPlusColonCheck = $token->previous && $token->previous->is(T_QUESTION_MARK) && $token->is(T_COLON);

            if (!$questionPlusColonCheck && $token->is($definition->separators)) {
                if (!array_key_exists($cluster->id, $options)) {
                    $openerIndex = $cluster->openerIndex;

                    if ($openerIndex < 0 || $definition->keepPrefixAndSuffix) {
                        $openerIndex = $definition->splitAfter ? $index + 1 : $index - 1;
                    }

                    $options[$cluster->id] = new Option($definition, $depth, $cluster->id, $openerIndex, $tokenCount);
                }

                ++$options[$cluster->id]->counter;

                $options[$cluster->id]->breakpointIndices[] = $index;
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS) && $depth > 0) {
                if (isset($options[$cluster->id]) && !$definition->keepPrefixAndSuffix) {
                    $options[$cluster->id]->closerIndex = $index;
                }

                --$depth;

                $cluster = $clusterManager->getPreviousCluster($depth);
            }
        }

        /** @var Option[] $options */
        $options = $this->flatten($options);

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
