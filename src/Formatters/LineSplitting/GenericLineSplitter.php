<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class GenericLineSplitter
{
    /** @var GenericLineSplitter\SeparatorGroup[] */
    private array $groups;

    public function __construct(
        private GenericLineSplitter\OptionsFinder  $optionsFinder,
        private GenericLineSplitter\OptionAssesser $assesser,
        private StatementSplitter                  $statementSplitter,
    )
    {
        // Sort the best separators on top
        $this->groups = [
            new GenericLineSplitter\SeparatorGroup([T_COMMA], true),
            new GenericLineSplitter\SeparatorGroup([T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR]),
            new GenericLineSplitter\SeparatorGroup([T_QUESTION_MARK, T_COLON]),
            new GenericLineSplitter\SeparatorGroup([T_PIPE]),
            new GenericLineSplitter\SeparatorGroup([T_PLUS, T_MINUS]),
            new GenericLineSplitter\SeparatorGroup([T_ASSIGNMENT]),
        ];
    }

    public function split(Statement $statement): bool
    {
        $options = [];

        foreach ($this->groups as $group) {
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
