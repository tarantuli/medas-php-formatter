<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
    StatementTypes\AttributeStatement,
    StatementTypes\Comment,
    StatementTypes\SwitchBranch,
    TokenGroups,
    TokenTree
};

#[Service]
readonly class BlankLineAdder
{
    public function __construct(
        private StatementTypeFinder $statementTypeFinder,
        private TokenGroups         $tokenGroups,
    )
    {
    }

    public function afterTypes(TokenTree $tree, array $afterTypes): void
    {
        $previousType = null;
        $previousStatement = null;

        foreach ($tree->statements() as $statement) {
            $type = $this->statementTypeFinder->for($statement);

            foreach ($afterTypes as $groupType) {
                if ($type instanceof $groupType) {
                    if ($statement->next()?->rootStatement !== $statement) {
                        $statement->blankLineAfter();
                    }

                    if ($previousType instanceof $groupType) {
                        if ($statement->lastToken()->is(Tokens::OPENING_BRACKETS)) {
                            // Keep a blank line beforea statement that's split on multiple lines
                        }
                        else {
                            $previousStatement->blankLineAfter(false);
                        }
                    }
                }
            }

            $previousStatement = $statement;
            $previousType = $type;
        }
    }

    public function beforeTypes(TokenTree $tree, array $beforeTypes): void
    {
        $previousStatement = null;

        foreach ($tree->statements() as $statement) {
            if ($statement->rootStatement) {
                // Only add a blank line before the root statement, not the others
                $previousStatement = $statement;

                continue;
            }

            $type = $this->statementTypeFinder->for($statement);

            foreach ($beforeTypes as $groupType) {
                $bypassCheck = $groupType === Comment::class
                    && $this->statementStartWithComment($statement);

                if (!$bypassCheck && !$type instanceof $groupType) {
                    // This statement is not of the given types
                    continue;
                }

                if ($previousStatement->block !== $statement->block) {
                    // There's never a blank line before the first statement of a block
                    continue;
                }

                $previousStatementType = $this->statementTypeFinder->for($previousStatement);

                if ($previousStatementType instanceof SwitchBranch) {
                    // There's never a blank line after a switch case/default statement
                    continue;
                }

                if ($previousStatementType instanceof Comment || $previousStatementType instanceof AttributeStatement) {
                    // There's never a blank line after a comment
                    continue;
                }

                $previousStatement->blankLineAfter();
            }

            $previousStatement = $statement;
        }
    }

    private function statementStartWithComment(Statement $statement): bool
    {
        return $statement->firstToken()->is($this->tokenGroups->comments());
    }
}
