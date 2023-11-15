<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\ConfigOptions\DumpOptionAssessment;
use Medas\PhpFormatter\Formatters\LineSplitting\Helpers\StatementSplitter;
use Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter;
use Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Breakpoints\Definition;
use Medas\PhpFormatter\Formatters\LineSplitting\LongLineSplitter\GenericLineSplitter\Options\Assessment;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class GenericLineSplitter
{
    public function __construct(
        private LongLineSplitter\GenericLineSplitter\Options\Finder   $optionFinder,
        private LongLineSplitter\GenericLineSplitter\Options\Assesser $assesser,
        private StatementSplitter                                     $statementSplitter,

        #[ConfigValue(DumpOptionAssessment::class)]
        private bool                                                  $dumpOptionAssessment,
    )
    {
    }

    public function split(
        Statement                                                      $statement,
        LongLineSplitter\GenericLineSplitter\Breakpoints\BreakpointSet $set,
    ): bool
    {
        foreach ($set->groupsOfDefinitions() as $definitions) {
            $options = $this->gatherOptions($definitions, $statement);
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

    /** @param Definition[] $definitions */
    private function gatherOptions(array $definitions, Statement $statement): array
    {
        $options = [];

        foreach ($definitions as $definition) {
            if (!$subOptions = $this->optionFinder->find($statement, $definition)) {
                continue;
            }

            $options = array_merge($options, $subOptions);
        }

        return $options;
    }

    private function selectBestOption(array $options, Statement $statement): LongLineSplitter\GenericLineSplitter\Options\Option|null
    {
        /** @var Assessment[] $assessments */
        $assessments = [];

        foreach ($options as $option) {
            $assessments[] = $this->assesser->assess($statement, $option);
        }

        if (!$assessments) {
            return null;
        }

        if ($this->dumpOptionAssessment) {
            // Inject it here, so it's not initialized when not needed
            service(LongLineSplitter\GenericLineSplitter\Options\AssessmentDumper::class)->dump($statement, $assessments);
        }

        usort(
            $assessments,
            fn(Assessment $a, Assessment $b) =>
                -1 * ($a->quality <=> $b->quality)
        );

        return $assessments[0]->quality === null ? null : $assessments[0]->option;
    }
}
