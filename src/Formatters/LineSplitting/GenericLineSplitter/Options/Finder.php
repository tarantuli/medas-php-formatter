<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\{
    GenericLineSplitter\Breakpoints,
    GenericLineSplitter\ClusterManager,
    TrailingCommaSplitter
};
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class Finder
{
    /** @return Option[] */
    public function find(Statement $statement, Breakpoints\Definition $group): array
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

            if ($token->is($group->separators)) {
                if (!array_key_exists($cluster->id, $options)) {
                    $openerIndex = $cluster->openerIndex;

                    if ($openerIndex === 0 || $group->keepPrefixAndSuffix) {
                        $openerIndex = $group->splitAfter ? $index + 1 : $index - 1;
                    }

                    $options[$cluster->id] = new Option($group, $depth, $cluster->id, $openerIndex);
                }

                ++$options[$cluster->id]->counter;

                $options[$cluster->id]->breakpointIndices[] = $index;
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS) && $depth > 0) {
                if (isset($options[$cluster->id]) && !$group->keepPrefixAndSuffix) {
                    $options[$cluster->id]->closerIndex = $index;
                }

                --$depth;

                $cluster = $clusterManager->getPreviousCluster($depth);
            }
        }

        /** @var Option[] $options */
        $options = $this->flatten($options);

        if ($group->maxDepth !== null) {
            foreach ($options as $i => $option) {
                if ($option->depth > $group->maxDepth) {
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
