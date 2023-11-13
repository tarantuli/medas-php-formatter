<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\ConfigOptions\DumpOptionAssessment;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class GenericLineSplitter
{
    public function __construct(
        private GenericLineSplitter\OptionsFinder  $optionsFinder,
        private GenericLineSplitter\OptionAssesser $assesser,
        private StatementSplitter                  $statementSplitter,

        #[ConfigValue(DumpOptionAssessment::class)]
        private bool                               $dumpOptionAssessment,
    )
    {
    }

    public function split(
        Statement                                        $statement,
        GenericLineSplitter\SeparatorGroups\SeparatorSet $set,
    ): bool
    {
        $options = $this->gatherOptions($set, $statement);
        $bestOption = $this->selectBestOption($options, $statement);

        if ($bestOption === null) {
            return false;
        }

        $this->statementSplitter->split(
            $statement,
            $bestOption->group->separators,
            $bestOption->group->splitAfter,
            $bestOption->openerIndex,
            $bestOption->closerIndex
        );

        return true;
    }

    private function gatherOptions(GenericLineSplitter\SeparatorGroups\SeparatorSet $set, Statement $statement): array
    {
        $options = [];

        foreach ($set->groups() as $group) {
            if (!$subOptions = $this->optionsFinder->find($statement, $group)) {
                continue;
            }

            $options = array_merge($options, $subOptions);
        }

        return $options;
    }

    private function selectBestOption(array $options, Statement $statement): GenericLineSplitter\Option|null
    {
        /** @var GenericLineSplitter\OptionAssessment[] $assessments */
        $assessments = [];

        foreach ($options as $option) {
            $assessments[] = $this->assesser->assess($statement, $option);
        }

        if (!$assessments) {
            return null;
        }

        if ($this->dumpOptionAssessment) {
            // Inject it here, so it's not initialized when not needed
            service(GenericLineSplitter\OptionAssessmentDumper::class)->dump($statement, $assessments);
        }

        usort(
            $assessments,

            fn(GenericLineSplitter\OptionAssessment $a, GenericLineSplitter\OptionAssessment $b) =>
                -1 * ($a->quality <=> $b->quality)
        );

        return $assessments[0]->quality === null ? null : $assessments[0]->option;
    }
}
