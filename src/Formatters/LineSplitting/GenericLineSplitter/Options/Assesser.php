<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{Helpers\Math, Tokens};
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class Assesser
{
    public function __construct(
        private Math $math,
    )
    {
    }

    public function assess(Statement $statement, Option $option): Assessment
    {
        $lengths = $this->gatherLengths($statement, $option);

        return new Assessment($option, $lengths, $this->quality($option, $lengths));
    }

    private function gatherLengths(Statement $statement, Option $option): array
    {
        $lengths = [];
        $currentLength = 0;
        $inPrefix = true;
        $inSuffix = false;
        $depth = 0;

        foreach ($statement as $index => $token) {
            if ($token->is(Tokens::OPENING_BRACKETS)) {
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

            if ($token->is(Tokens::CLOSING_BRACKETS)) {
                --$depth;
            }
        }

        $lengths[] = $currentLength;

        return $lengths;
    }

    private function quality(Option $option, array $lengths): float|null
    {
        if (count($lengths) < 2) {
            return null;
        }

        $depth = $option->depth < 1 ? 1 : $option->depth + 1;

        $standardDeviation = $this->math->standardDeviation($lengths);

        if ($standardDeviation < 1e-6) {
            return null;
        }

        return array_sum($lengths)
            / count($lengths)
            / $standardDeviation
            / pow($depth, 2);
    }
}
