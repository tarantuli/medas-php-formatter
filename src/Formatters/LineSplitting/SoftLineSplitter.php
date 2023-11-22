<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\{ConfigOptions\SoftMaxLineLength,
    Formatters\BaseFormatter,
    Formatters\LineSplitting\Helpers\LengthCounter,
    Job};
use Medas\PhpTokenizer\{Statement,
    StatementTypeFinder,
    StatementTypes\ClassDeclaration,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class SoftLineSplitter extends BaseFormatter
{
    public function __construct(
        #[ConfigValue(SoftMaxLineLength::class)]
        private int                 $softMaxLineLength,
        private GenericLineSplitter $genericLineSplitter,
        private LengthCounter       $lengthCounter,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 690;
    }

    public function format(Job $job): void
    {
        $counter = 0;

        do {
            $foundSomething = $this->findSomethingToSplit($job);
        } while ($foundSomething && ++$counter < 256);
    }

    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            if ($statement === $statement->next()?->rootStatement) {
                // This statement has already been split, and this is the prefix;
                // Don't split it any further
                continue;
            }

            $length = $this->lengthCounter->count($statement);

            if ($length <= $this->softMaxLineLength) {
                continue;
            }

            $type = $this->typeFinder->for($statement);

            if ($type instanceof ClassDeclaration || $type instanceof FunctionDeclaration) {
                continue;
            }

            if ($this->splitStatement($statement)) {
                return true;
            }
        }

        return false;
    }

    private function splitStatement(Statement $statement): bool
    {
        return $this->genericLineSplitter->split(
            $statement,
            GenericLineSplitter\Breakpoints\SoftLineSplitSet::instance()
        );
    }
}
