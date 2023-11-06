<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Core\Attributes\Service;

#[Service]
class ClusterManager
{
    private int $currentCluster = 0;

    /** @var int[] */
    private array $lastClusterPerDepth = [0 => 0];

    public function getNextCluster(int $depth): int
    {
        ++$this->currentCluster;

        $this->lastClusterPerDepth[$depth] = $this->currentCluster;

        return $this->currentCluster;
    }

    public function getPreviousCluster(int $depth): int
    {
        return $this->lastClusterPerDepth[$depth];
    }
}
