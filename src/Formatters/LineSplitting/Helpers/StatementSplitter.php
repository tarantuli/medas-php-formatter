<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\LineSplitting\TrailingCommaSplitter;
use Medas\PhpTokenizer\{Statement, StatementTypeFinder, StatementTypes\SwitchBranch, StatementTypes\UseClassStatement};

#[Service]
readonly class StatementSplitter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function split(
        Statement $statement,
        array     $separators,
        bool      $splitAfter,
        int       $openerIndex,
        int       $closerIndex,
        int       $additionalDepth
    ): void
    {
        $this->addBlankLineBefore($statement);

        $currentStatement = null;
        $depth = 0;

        if ($statement->firstToken()->is(TrailingCommaSplitter::BRACKETS)
                && $statement->lastToken()->is(TrailingCommaSplitter::CLOSERS)) {
            // Statements that begin and end with brackets are hard to process correctly elsewhere,
            // so start at a depth of -1 here
            $closerIndex = $statement->tokenCount() - 1;
        }

        if ($statement->lastToken()->is(TrailingCommaSplitter::BRACKETS)
                && $statement->lastToken()->previous->lineBreakAfter
                && $closerIndex === $statement->tokenCount()) {
            // The last token is an opening bracket, and there's a line break before it:
            // put this bracket in the extracted trailing tokens to maintain depth
            $closerIndex--;
        }

        $this->extractTrailingTokens($statement, $statement->tokenCount() - 1, $closerIndex);

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);
            $questionPlusColonCheck = $token->previous && $token->previous->is(T_QUESTION_MARK) && $token->is(T_COLON);

            $statement->removeToken($token);

            $text = $token->text;

            if (!$splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);
                }

                $currentStatement->prependToken($token);
            }

            if (!$questionPlusColonCheck && $token->is($separators) && $depth === 0 && $i > $openerIndex + 1) {
                $currentStatement = $this->startNewStatement($statement, $additionalDepth);
            }

            if (!$token->inAttribute && in_array($text, TrailingCommaSplitter::CLOSERS)) {
                ++$depth;
            }

            if (!$token->inAttribute && in_array($text, TrailingCommaSplitter::BRACKETS)) {
                --$depth;
            }

            if ($splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);
                }

                if ($currentStatement->tokenCount() === 0
                        && $currentStatement->next()
                        && $currentStatement->next()->firstToken()->is([T_ATTRIBUTE, T_COMMENT, T_DOC_COMMENT])) {
                    $currentStatement->blankLineAfter();
                }

                if ($token->is(T_DOC_COMMENT)) {
                    $token->lineBreakAfter();
                }

                $currentStatement->prependToken($token);
            }
        }
    }

    private function extractTrailingTokens(Statement $statement, int $lastIndex, int $closerIndex): void
    {
        if ($lastIndex < $closerIndex) {
            return;
        }

        $finalStatement = new Statement($statement->block);
        $finalStatement->rootStatement = $statement;
        $finalStatement->additionalDepth = $statement->additionalDepth;

        if ($statement->blankLineAfter) {
            $finalStatement->blankLineAfter();

            $statement->blankLineAfter(false);
        }

        $statement->block->insertStatementAfter($finalStatement, $statement);

        for ($i = $lastIndex; $i >= $closerIndex; --$i) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            $finalStatement->prependToken($token);
        }
    }

    private function startNewStatement(Statement $statement, int $additionalDepth): Statement
    {
        $newStatement = new Statement($statement->block);
        $newStatement->rootStatement = $statement;

        $statement->block->insertStatementAfter($newStatement, $statement);

        $newStatement->additionalDepth = $statement->additionalDepth + $additionalDepth;

        if ($statement->blankLineAfter) {
            $newStatement->blankLineAfter();

            $statement->blankLineAfter(false);
        }

        return $newStatement;
    }

    private function addBlankLineBefore(Statement $statement): void
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
