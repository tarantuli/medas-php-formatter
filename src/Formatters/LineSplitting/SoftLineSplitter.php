<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\{ConfigOptions\SoftMaxLineLength, Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
    StatementTypes\ClassDeclaration,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class SoftLineSplitter extends BaseFormatter
{
    public function __construct(
        #[ConfigValue(SoftMaxLineLength::class)]
        private int                      $softMaxLineLength,
        private GenericLineSplitter      $genericLineSplitter,
        private Helpers\StatementLengths $lengthCounter,
        private StatementTypeFinder      $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 500;
    }

    public function format(Job $job): void
    {
        whileTrue(fn() => $this->findSomethingToSplit($job));
    }

    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            if (isset($job->checkedBySoftLineSplitter[spl_object_id($statement)])) {
                continue;
            }

            if ($statement === $statement->next()?->rootStatement) {
                // This statement has already been split, and this is the prefix;
                // Don't split it any further
                continue;
            }

            $length = $this->lengthCounter->measure($statement);

            if ($length <= $this->softMaxLineLength) {
                $job->checkedBySoftLineSplitter[spl_object_id($statement)] = true;

                continue;
            }

            $type = $this->typeFinder->for($statement);

            if ($type instanceof ClassDeclaration || $type instanceof FunctionDeclaration) {
                continue;
            }

            if ($this->splitStatement($job, $statement)) {
                return true;
            }
        }

        return false;
    }

    private function splitStatement(Job $job, Statement $statement): bool
    {
        return $this->genericLineSplitter->split($statement, $job->settings->softLineSet);
    }
}
