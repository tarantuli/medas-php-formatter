<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class OptionsFinder
{
    /** @return Option[] */
    public function find(Statement $statement, array $separators, bool $splitAfter): array
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

            if ($token->is($separators)) {
                if (!array_key_exists($cluster->id, $options)) {
                    $openerIndex = $cluster->openerIndex;

                    if ($openerIndex === 0) {
                        $openerIndex = $splitAfter ? $index + 1 : $index - 1;
                    }

                    $options[$cluster->id] = new Option($separators, $splitAfter, $depth, $cluster->id, $openerIndex);
                }

                ++$options[$cluster->id]->counter;

                $options[$cluster->id]->separatorIndices[] = $index;
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS) && $depth > 0) {
                if (isset($options[$cluster->id])) {
                    $options[$cluster->id]->closerIndex = $index;
                }

                --$depth;

                $cluster = $clusterManager->getPreviousCluster($depth);
            }
        }

        return $this->flatten($options);
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
