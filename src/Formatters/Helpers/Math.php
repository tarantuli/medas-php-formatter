<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Helpers;

use Medas\Core\Attributes\Service;

#[Service]
readonly class Math
{
    public function standardDeviation(array $values): float|null
    {
        if (count($values) < 2) {
            return null;
        }

        $average = array_sum($values) / count($values);
        $variance = 0.0;

        foreach ($values as $i) {
            $variance += pow($i - $average, 2);
        }

        return sqrt($variance) / sqrt(count($values));
    }
}
