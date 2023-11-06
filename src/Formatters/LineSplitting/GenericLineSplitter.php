<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\SeparatorGroup;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class GenericLineSplitter
{
    /** @var SeparatorGroup[] */
    private array $groups;

    public function __construct(
        private GenericLineSplitter\OptionsFinder  $optionsFinder,
        private GenericLineSplitter\OptionAssesser $assesser,
        private StatementSplitter                  $statementSplitter,
    )
    {
        // Sort the best separators on top
        $this->groups = [
            new SeparatorGroup([T_COMMA], true),
            new SeparatorGroup([T_BOOLEAN_AND, T_BOOLEAN_OR, T_LOGICAL_AND, T_LOGICAL_OR], false),
            new SeparatorGroup([T_DOUBLE_ARROW], false),
        ];
    }

    public function split(Statement $statement): bool
    {
        foreach ($this->groups as $group) {
            if (!$options = $this->optionsFinder->find($statement, $group->separators)) {
                continue;
            }

            $bestQuality = null;
            $bestOption = null;

            foreach ($options as $option) {
                $quality = $this->assesser->assess($statement, $option);

                if ($bestQuality === null || $quality > $bestQuality || ($quality === $bestQuality && $option->depth < $bestOption->depth)) {
                    $bestQuality = $quality;
                    $bestOption = $option;
                }
            }

            $this->statementSplitter->split(
                $statement,
                $bestOption->separators,
                $group->splitAfter,
                $bestOption->openerIndex,
                $bestOption->closerIndex
            );

            return true;
        }

        return false;
    }
}
