<?php

declare(strict_types=1);

use Medas\Charts\{Chart, Elements};

class ChartFactoryx
{
    public function create(): Chart
    {
        $chart = new Chart();

        $chart->xAxis = new Elements\XAxis();
        $chart->yAxis = new Elements\YAxis();
        $chart->y2Axis = new Elements\Y2Axis();

        return $chart;
    }
}
