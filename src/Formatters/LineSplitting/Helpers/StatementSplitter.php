<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\{Statement, TokenGroups};

#[Service]
readonly class StatementSplitter
{
    private array $commentTokens;

    public function __construct(
        private StatementSplitter\BlankLineInserter $blankLineInserter,
        private TokenGroups                         $tokenGroups,
    )
    {
        $this->commentTokens = $this->tokenGroups->comments();
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
        $this->blankLineInserter->addBlankLineBefore($statement);

        $currentStatement = null;
        $depth = $this->determineInitialDepth($statement);

        if ($statement->lastToken()->is(Tokens::CLOSING_BRACKETS)) {
            // Statements that end with closing brackets are hard to process correctly elsewhere,
            // so skip the last token here
            $closerIndex = $statement->tokenCount() - 1;
        }

        if ($statement->lastToken()->is(Tokens::OPENING_BRACKETS)
                && $statement->lastToken()->previous->lineBreakAfter
                && $closerIndex === $statement->tokenCount()) {
            // The last token is an opening bracket, and there's a line break before it:
            // put this bracket in the extracted trailing tokens to maintain depth
            $closerIndex--;
        }

        $this->extractTrailingTokens($statement, $statement->tokenCount() - 1, $closerIndex);

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);

            $questionPlusColonCheck = $token->previous
                && $token->previous->is(T_QUESTION_MARK)
                && $token->is(T_COLON);

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

            if (!$token->inAttribute && in_array($text, Tokens::CLOSING_BRACKETS)) {
                ++$depth;
            }

            if (!$token->inAttribute && in_array($text, Tokens::OPENING_BRACKETS)) {
                --$depth;
            }

            if ($splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);
                }

                if ($currentStatement->tokenCount() === 0
                        && $currentStatement->next()
                        && $currentStatement->next()->firstToken()->is($this->commentTokens)) {
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

    private function determineInitialDepth(Statement $statement): int
    {
        $openers = 0;
        $closers = 0;
        $lastToken = $statement->lastToken();

        foreach ($statement as $token) {
            if ($token !== $lastToken && $token->is(Tokens::OPENING_BRACKETS)) {
                ++$openers;
            }
            elseif ($token->is(Tokens::CLOSING_BRACKETS)) {
                ++$closers;
            }
        }

        return $openers > $closers ? $openers - $closers : 0;
    }
}
