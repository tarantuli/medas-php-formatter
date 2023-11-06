<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class OptionsFinder
{
    public function __construct(
        private ClusterManager $clusterManager,
    )
    {
    }

    /** @return Option[] */
    public function find(Statement $statement, array $separators): array
    {
        /** @var Option[][] $options */
        $options = [];

        $cluster = 0;
        $clusterOpener = 0;
        $depth = 0;

        foreach ($statement as $index => $token) {
            if ($token->is(TrailingCommaSplitter::BRACKETS)) {
                ++$depth;
                $cluster = $this->clusterManager->getNextCluster($depth);

                $clusterOpener = $index;
            }

            if ($token->is($separators)) {
                if (!array_key_exists($cluster, $options)) {
                    $options[$cluster] = new Option($separators, $depth, $cluster, $clusterOpener);
                }

                ++$options[$cluster]->counter;
                $options[$cluster]->separatorIndices[] = $index;
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS)) {
                if (isset($options[$cluster])) {
                    $options[$cluster]->closerIndex = $index;
                }

                --$depth;
                $cluster = $this->clusterManager->getPreviousCluster($depth);
            }
        }

        return $this->flatten($options);
    }

    private function flatten(array $array): array
    {
        $return = [];

        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }
}
