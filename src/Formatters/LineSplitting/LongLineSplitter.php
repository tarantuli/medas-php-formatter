<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\{ConfigValue, Service};
use Medas\PhpFormatter\{ConfigOptions\MaxLineLength, Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
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
    )
    {
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
            $length = $this->statementLength($statement);

            if ($length <= $this->maxLineLength) {
                continue;
            }

            if ($this->splitStatement($statement)) {
                return true;
            }
        }

        return false;
    }

    private function statementLength(Statement $statement): int
    {
        $length = $statement->block->depth * 4;
        $previousToken = null;

        foreach ($statement as $token) {
            if (!$token->is([T_DOC_COMMENT, T_COMMENT])) {
                $length += strlen($token->text);
            }

            if ($previousToken && $previousToken->spaceAfter) {
                $length++;
            }

            $previousToken = $token;
        }

        return $length;
    }

    private function splitStatement(Statement $statement): bool
    {
        $type = $this->typeFinder->for($statement);

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
