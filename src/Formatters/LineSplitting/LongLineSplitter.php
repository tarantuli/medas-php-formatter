<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\{ConfigOptions\MaxLineLength, Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
    StatementTypes\ClassDeclaration,
    StatementTypes\ControlStatement,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class LongLineSplitter extends BaseFormatter
{
    public function __construct(
        #[ConfigValue(MaxLineLength::class)]
        private int                         $maxLineLength,
        private StatementTypeFinder         $typeFinder,
        private GenericLineSplitter         $genericLineSplitter,
        private FunctionDeclarationSplitter $functionDeclarationSplitter,
        private Helpers\StatementLengths    $lengthCounter,
    )
    {
    }

    public function priority(): int
    {
        return 600;
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

            $length = $this->lengthCounter->measure($statement);

            if ($length <= $this->maxLineLength) {
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
        $type = $this->typeFinder->for($statement);

        if ($type instanceof ClassDeclaration) {
            return $this->genericLineSplitter->split(
                $statement,
                GenericLineSplitter\Breakpoints\ClassDeclarationSet::instance()
            );
        }

        if ($type instanceof FunctionDeclaration) {
            return $this->functionDeclarationSplitter->split($statement);
        }

        if ($type instanceof ControlStatement) {
            return $this->genericLineSplitter->split(
                $statement,
                GenericLineSplitter\Breakpoints\ControlStatementSet::instance()
            );
        }

        return $this->genericLineSplitter->split(
            $statement,
            GenericLineSplitter\Breakpoints\GenericLineSet::instance()
        );
    }
}
