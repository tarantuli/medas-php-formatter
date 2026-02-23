<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\ConfigOptions\DumpOptionAssessment;
use Medas\PhpTokenizer\Statement;
use Medas\ServiceManager\ServiceManager;

#[Service]
readonly class GenericLineSplitter
{
    public function __construct(
        private GenericLineSplitter\Options\Assesser $assesser,
        private GenericLineSplitter\Options\Finder   $optionFinder,
        private Helpers\StatementSplitter            $statementSplitter,
        private ServiceManager                       $serviceManager,

        #[ConfigValue(DumpOptionAssessment::class)]
        private bool                                 $dumpOptionAssessment,
    )
    {
    }

    public function split(
        Statement                         $statement,
        GenericLineSplitter\BreakpointSet $set,
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
                $bestOption->breakpointDefinition->additionalDepth,
            );

            return true;
        }

        return false;
    }

    /** @param GenericLineSplitter\BreakpointDefinition[] $definitions */
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
            $this->serviceManager->resolve(GenericLineSplitter\Options\AssessmentDumper::class)->dump(
                $statement,
                $assessments
            );
        }

        usort(
            $assessments,
            fn(GenericLineSplitter\Options\Assessment $a, GenericLineSplitter\Options\Assessment $b) =>
                -1 * ($a->quality <=> $b->quality
            )
        );

        return $assessments[0]->quality === null ? null : $assessments[0]->option;
    }
}
