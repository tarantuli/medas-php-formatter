<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\{ConfigOptions\MaxLineLength, Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Contexts\PropertyHook,
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
        private FunctionDeclarationSplitter $functionDeclarationSplitter,
        private GenericLineSplitter         $genericLineSplitter,
        private Helpers\StatementLengths    $lengthCounter,
        private StatementTypeFinder         $typeFinder,

        #[ConfigValue(MaxLineLength::class)]
        private int                         $maxLineLength,
    )
    {
    }

    public function priority(): int
    {
        return 600;
    }

    public function format(Job $job): void
    {
        whileTrue(fn() => $this->findSomethingToSplit($job));
    }

    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            if (isset($job->checkedByLongLineSplitter[spl_object_id($statement)])) {
                continue;
            }

            // Don't split statements inside hook bodies (set { }, get { }).
            // Hook-block-level statements (get =>, set =>, etc.) can be split if too long,
            // but content inside a long-form hook body is left as-is.
            if ($statement->firstToken()?->context instanceof PropertyHook
                    && $statement->block->opener?->firstToken()?->context instanceof PropertyHook) {
                $job->checkedByLongLineSplitter[spl_object_id($statement)] = true;

                continue;
            }

            if ($statement === $statement->next()?->rootStatement) {
                // This statement has already been split, and this is the prefix;
                // Don't split it any further
                $job->checkedByLongLineSplitter[spl_object_id($statement)] = true;

                continue;
            }

            $length = $this->lengthCounter->measure($statement);

            if ($length <= $this->maxLineLength) {
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
        $type = $this->typeFinder->for($statement);

        if ($type instanceof ClassDeclaration) {
            return $this->genericLineSplitter->split(
                $statement,
                $job->settings->classDeclarationSet
            );
        }

        if ($type instanceof FunctionDeclaration) {
            return $this->functionDeclarationSplitter->split($statement);
        }

        if ($type instanceof ControlStatement) {
            return $this->genericLineSplitter->split(
                $statement,
                $job->settings->controlStatementSet
            );
        }

        return $this->genericLineSplitter->split($statement, $job->settings->genericLineSet);
    }
}
