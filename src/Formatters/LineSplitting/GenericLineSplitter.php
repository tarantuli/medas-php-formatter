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
        private GenericLineSplitter\Options\Finder   $optionFinder,
        private GenericLineSplitter\Options\Assesser $assesser,
        private StatementSplitter                    $statementSplitter,

        #[ConfigValue(DumpOptionAssessment::class)]
        private bool                                 $dumpOptionAssessment,
    )
    {
    }

    public function split(
        Statement                                     $statement,
        GenericLineSplitter\Breakpoints\BreakpointSet $set,
    ): bool
    {
        foreach ($set->groupsOfGroups() as $group) {
            $options = $this->gatherOptions($group, $statement);
            $bestOption = $this->selectBestOption($options, $statement);

            if ($bestOption === null) {
                continue;
            }

            $this->statementSplitter->split(
                $statement,
                $bestOption->breakpointDefinition->separators,
                $bestOption->breakpointDefinition->splitAfter,
                $bestOption->openerIndex,
                $bestOption->closerIndex,
                $bestOption->breakpointDefinition->keepPrefixAndSuffix ? 2 : 1,
            );

            return true;
        }

        return false;
    }

    /** @param GenericLineSplitter\Breakpoints\Definition[] $groups */
    private function gatherOptions(array $groups, Statement $statement): array
    {
        $options = [];

        foreach ($groups as $group) {
            if (!$subOptions = $this->optionFinder->find($statement, $group)) {
                continue;
            }

            $options = array_merge($options, $subOptions);
        }

        return $options;
    }

    private function selectBestOption(array $options, Statement $statement): GenericLineSplitter\Options\Option|null
    {
        /** @var GenericLineSplitter\Options\Assessment[] $assessments */
        $assessments = [];

        foreach ($options as $option) {
            $assessments[] = $this->assesser->assess($statement, $option);
        }

        if (!$assessments) {
            return null;
        }

        if ($this->dumpOptionAssessment) {
            // Inject it here, so it's not initialized when not needed
            service(GenericLineSplitter\Options\AssessmentDumper::class)->dump($statement, $assessments);
        }

        usort(
            $assessments,
            fn(GenericLineSplitter\Options\Assessment $a, GenericLineSplitter\Options\Assessment $b) =>
                -1 * ($a->quality <=> $b->quality)
        );

        return $assessments[0]->quality === null ? null : $assessments[0]->option;
    }
}
