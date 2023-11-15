<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\{LongLineSplitter\GenericLineSplitter\Breakpoints\Definition,
    LongLineSplitter\GenericLineSplitter\ClusterManager,
    TrailingCommaSplitter};
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

        foreach ($statement as $index => $token) {
            if ($token->is(TrailingCommaSplitter::BRACKETS)) {
                ++$depth;

                $cluster = $clusterManager->getNextCluster($depth, $index);
            }

            if ($token->is($definition->separators)) {
                if (!array_key_exists($cluster->id, $options)) {
                    $openerIndex = $cluster->openerIndex;

                    if ($openerIndex === 0 || $definition->keepPrefixAndSuffix) {
                        $openerIndex = $definition->splitAfter ? $index + 1 : $index - 1;
                    }

                    $options[$cluster->id] = new Option($definition, $depth, $cluster->id, $openerIndex);
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
