<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Statement;
use Medas\PhpTokenizer\StatementTypeFinder;
use Medas\PhpTokenizer\StatementTypes\{AttributeStatement, Comment, StatementType, SwitchBranch};
use Medas\PhpTokenizer\TokenTree;

#[Service]
class BlankLineAdder
{
    private Statement|null $previousStatement = null;
    private StatementType|null $previousType = null;

    public function __construct(
        private readonly StatementTypeFinder $statementTypeFinder,
    )
    {
    }

    public function afterTypes(TokenTree $tree, array $afterTypes): void
    {
        foreach ($tree->statements() as $statement) {
            $type = $this->statementTypeFinder->for($statement);

            foreach ($afterTypes as $groupType) {
                if ($type instanceof $groupType) {
                    $statement->blankLineAfter = true;

                    if ($this->previousType instanceof $groupType) {
                        /** @noinspection PhpFieldImmediatelyRewrittenInspection */
                        $this->previousStatement->blankLineAfter = false;
                    }
                }
            }

            $this->previousStatement = $statement;
            $this->previousType = $type;
        }
    }

    public function beforeTypes(TokenTree $tree, array $beforeTypes): void
    {
        foreach ($tree->statements() as $statement) {
            if ($statement->rootStatement) {
                // Only add a blank line before the root statement, not the others
                continue;
            }

            $type = $this->statementTypeFinder->for($statement);

            foreach ($beforeTypes as $groupType) {
                $bypassCheck = $groupType === Comment::class && $this->statementStartWithComment($statement);

                if (!$bypassCheck && !$type instanceof $groupType) {
                    // This statement is not of the given types
                    continue;
                }

                if ($this->previousStatement->block !== $statement->block) {
                    // There's never a blank line before the first statement of a block
                    continue;
                }

                $previousStatementType = $this->statementTypeFinder->for($this->previousStatement);

                if ($previousStatementType instanceof SwitchBranch) {
                    // There's never a blank line after a switch case/default statement
                    continue;
                }

                if ($previousStatementType instanceof Comment || $previousStatementType instanceof AttributeStatement) {
                    // There's never a blank line after a comment
                    continue;
                }

                $this->previousStatement->blankLineAfter = true;
            }

            $this->previousStatement = $statement;
        }
    }

    private function statementStartWithComment(Statement $statement): bool
    {
        return $statement->firstToken()->is([T_ATTRIBUTE, T_DOC_COMMENT]);
    }
}
