<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class OptionAssesser
{
    public function assess(Statement $statement, Option $option): float
    {
        $lengths = [];
        $currentLength = 0;
        $inPrefix = true;
        $inSuffix = false;

        $depth = 0;

        foreach ($statement as $index => $token) {
            if ($token->is(TrailingCommaSplitter::BRACKETS)) {
                ++$depth;
            }

            $currentLength += strlen($token->text) + $token->extraSpacesAfter;

            if ($inPrefix && $index === $option->openerIndex) {
                $lengths[] = $currentLength;
                $currentLength = 0;
                $inPrefix = false;
            }

            if (!$inPrefix && !$inSuffix) {
                if (in_array($index, $option->separatorIndices, true)) {
                    $lengths[] = $currentLength;
                    $currentLength = 0;
                }

                if ($index === $option->closerIndex) {
                    $inSuffix = true;
                }
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS)) {
                --$depth;
            }
        }

        $lengths[] = $currentLength;

        return array_sum($lengths) / count($lengths) / $this->standard_deviation($lengths);
    }

    function standard_deviation($aValues)
    {
        $fMean = array_sum($aValues) / count($aValues);
        //print_r($fMean);
        $fVariance = 0.0;
        foreach ($aValues as $i)
        {
            $fVariance += pow($i - $fMean, 2);

        }
        $size = count($aValues) - 1;
        return (float) sqrt($fVariance)/sqrt($size);
    }
}
