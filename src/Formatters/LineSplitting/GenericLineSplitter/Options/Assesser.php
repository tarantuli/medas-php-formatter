<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class Assesser
{
    public function assess(Statement $statement, Option $option): Assessment
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

            if ($option->breakpointDefinition->splitAfter) {
                $currentLength += strlen($token->text) + $token->extraSpacesAfter;

                if ($inPrefix && $index === $option->openerIndex) {
                    $lengths[] = $currentLength;
                    $currentLength = 0;
                    $inPrefix = false;
                }

                if (!$inPrefix && !$inSuffix) {
                    if (in_array($index, $option->breakpointIndices, true)) {
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
                    if (in_array($index, $option->breakpointIndices, true)) {
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

        return new Assessment($option, $lengths, $this->quality($option, $lengths));
    }

    private function quality(Option $option, array $lengths): float|null
    {
        if (count($lengths) < 2) {
            return null;
        }

        $depth = $option->depth < 1 ? 1 : $option->depth + 1;

        return array_sum($lengths)
            / count($lengths)
            / $this->standardDeviation($lengths)
            / pow($depth, 2);
    }

    private function standardDeviation(array $values): float|null
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
