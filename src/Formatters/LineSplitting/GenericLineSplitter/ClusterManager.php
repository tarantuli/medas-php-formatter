<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

class ClusterManager
{
    private int $id;
    private Cluster $currentCluster;

    /** @var Cluster[] */
    private array $lastClusterPerDepth;

    public function __construct()
    {
        $this->id = 0;
        $this->currentCluster = new Cluster(0, -1);
        $this->lastClusterPerDepth = [0 => $this->currentCluster];
    }

    public function currentCluster(): Cluster
    {
        return $this->currentCluster;
    }

    public function getNextCluster(int $depth, int $index): Cluster
    {
        $this->lastClusterPerDepth[$depth] = $this->currentCluster = new Cluster(++$this->id, $index);

        return $this->currentCluster;
    }

    public function getPreviousCluster(int $depth): Cluster
    {
        return $this->lastClusterPerDepth[$depth];
    }
}
