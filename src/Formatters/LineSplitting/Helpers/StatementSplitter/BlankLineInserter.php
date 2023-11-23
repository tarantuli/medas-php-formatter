<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers\StatementSplitter;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
    StatementTypes\SwitchBranch,
    StatementTypes\UseClassStatement
};

#[Service]
readonly class BlankLineInserter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function addBlankLineBefore(Statement $statement): void
    {
        if (!$previousStatement = $statement->previous()) {
            return;
        }

        $previousStatementType = $this->typeFinder->for($previousStatement);

        if ($previousStatementType instanceof SwitchBranch) {
            // No blank line after the start of a case statement
            return;
        }

        if ($this->typeFinder->for($statement) instanceof UseClassStatement
                && $previousStatementType instanceof UseClassStatement) {
            // No blank lines between a use statement and a split use statement
            return;
        }

        if ($statement->rootStatement === $previousStatement) {
            // No blank lines between a root statement and its children
            return;
        }

        if ($previousStatement->lastToken()->is(T_COMMA)) {
            // No blank lines between comma separated listing
            return;
        }

        if ($statement->block === $previousStatement->block) {
            $previousStatement->blankLineAfter();
        }
    }
}
