<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\Comment;
use Medas\PhpBeautifier\Tokens\StatementTypes\StatementType;
use Medas\PhpBeautifier\Tokens\StatementTypes\SwitchBranch;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLineAdder
{
    private Statement|null $previousStatement = null;
    private StatementType|null $previousType = null;

    public function __construct(
        private StatementTypeFinder $statementTypeFinder,
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
            $type = $this->statementTypeFinder->for($statement);

            foreach ($beforeTypes as $groupType) {
                if (!$type instanceof $groupType) {
                    // This statement is not of the given types
                    continue;
                }

                if ($this->previousStatement->block !== $statement->block) {
                    // There's never a blank line before the first statement of a block
                    continue;
                }

                if ($this->statementTypeFinder->for($this->previousStatement) instanceof SwitchBranch) {
                    // There's never a blank line after a switch case/default statement
                    continue;
                }

                if ($this->statementTypeFinder->for($this->previousStatement) instanceof Comment) {
                    // There's never a blank line after a comment
                    continue;
                }

                $this->previousStatement->blankLineAfter = true;
            }

            $this->previousStatement = $statement;
        }
    }
}
