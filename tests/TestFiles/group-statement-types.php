<?php

declare(strict_types=1);

use Datasets;

class GroupStatementTypes
{
    public function addMovingAverage(
        Datasets\AbstractDataset $sourceSet,
        int                      $span,
        bool                     $addTails = true,
        string                   $name = null,
                                 $typeLess = null
    )
    {
        $dataset = new Datasets\MovingAverage($this->settings, $name);

        $dataset->setSourceSet($this->getDataset($sourceSet));
        $dataset->setSpan($span);
        $dataset->setAddTails($addTails);

        $this->datasets[$dataset->getName()] = $dataset;
    }

    public function addMovingAverage2(Datasets\AbstractDataset $sourceSet, float $width, string $name = null)
    {
        $dataset = new Datasets\MovingAverage2($this->settings, $name);

        $dataset->setSourceSet($this->getDataset($sourceSet));
        $dataset->setWidth($width);

        $this->datasets[$dataset->getName()] = $dataset;
    }

    private function a(): true
    {
        $mimetype->setHeader('Access-Control-Allow-Origin', '*');

        $manager->setHeader('Access-Control-Allow-Origin', '*');
        $manager->setHeader('Content-Type', $mimetype);
        $manager->setHeader('Content-Disposition: inline; filename="%s"', $fileName);

        echo $file->content();

        return true;
    }
}
