<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class OptionAssesser
{
    public function assess(Statement $statement, Option $option): float|null
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

            if ($option->splitAfter) {
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
            }
            else {
                if (!$inPrefix && !$inSuffix) {
                    if (in_array($index, $option->separatorIndices, true)) {
                        if ($currentLength) {
                            $lengths[] = $currentLength;
                        }

                        $currentLength = 0;
                    }

                    if ($index === $option->closerIndex) {
                        $inSuffix = true;
                    }
                }

                $currentLength += strlen($token->text) + $token->extraSpacesAfter;

                if ($inPrefix && $index === $option->openerIndex) {
                    if ($currentLength) {
                        $lengths[] = $currentLength;
                    }

                    $currentLength = 0;
                    $inPrefix = false;
                }
            }

            if ($token->is(TrailingCommaSplitter::CLOSERS)) {
                --$depth;
            }
        }

        $lengths[] = $currentLength;

        if (count($lengths) < 2) {
            return null;
        }

        return $this->quality($lengths);
    }

    private function standardDeviation(array $values): float
    {
        $average = array_sum($values) / count($values);
        $variance = 0.0;

        foreach ($values as $i) {
            $variance += pow($i - $average, 2);
        }

        return sqrt($variance) / sqrt(count($values));
    }

    private function quality(array $lengths): float
    {
        return array_sum($lengths) / count($lengths) / $this->standardDeviation($lengths);
    }
}
