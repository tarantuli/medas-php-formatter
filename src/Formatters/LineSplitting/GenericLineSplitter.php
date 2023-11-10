<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroups\SeparatorSet;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class GenericLineSplitter
{
    public function __construct(
        private GenericLineSplitter\OptionsFinder  $optionsFinder,
        private GenericLineSplitter\OptionAssesser $assesser,
        private StatementSplitter                  $statementSplitter,
    )
    {
    }

    public function split(Statement $statement, SeparatorSet $set): bool
    {
        $options = [];

        foreach ($set->groups() as $group) {
            if (!$subOptions = $this->optionsFinder->find($statement, $group->separators, $group->splitAfter)) {
                continue;
            }

            $options = array_merge($options, $subOptions);
        }

        $bestQuality = null;
        $bestOption = null;

        foreach ($options as $option) {
            $quality = $this->assesser->assess($statement, $option);

            if ($quality === null) {
                continue;
            }

            if ($bestQuality === null || $quality > $bestQuality || (
                    $quality === $bestQuality
                    && $option->depth < $bestOption->depth
                )) {
                $bestQuality = $quality;
                $bestOption = $option;
            }
        }

        if ($bestQuality === null || $bestQuality === 0) {
            return false;
        }

        $this->statementSplitter->split(
            $statement,
            $bestOption->separators,
            $bestOption->splitAfter,
            $bestOption->openerIndex,
            $bestOption->closerIndex
        );

        return true;
    }
}
