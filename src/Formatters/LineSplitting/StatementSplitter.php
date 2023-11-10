<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class StatementSplitter
{
    public function split(
        Statement $statement,
        array     $separators,
        bool      $splitAfter,
        int       $openerIndex,
        int|null  $closerIndex
    ): void
    {
        $this->addBlankLineBefore($statement);

        if ($closerIndex === null) {
            $closerIndex = $statement->tokenCount();
        }

        $this->extractTrailingTokens($statement, $statement->tokenCount() - 1, $closerIndex);

        $currentStatement = null;
        $depth = 0;

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            $text = $token->text;

            if (!$token->inAttribute && in_array($text, TrailingCommaSplitter::CLOSERS)) {
                ++$depth;
            }

            if (!$splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement);
                }

                $currentStatement->prependToken($token);
            }

            if ($token->is($separators) && $depth === 0 && $i > $openerIndex + 1) {
                $currentStatement = $this->startNewStatement($statement);
            }

            if (!$token->inAttribute && in_array($text, TrailingCommaSplitter::BRACKETS)) {
                --$depth;
            }

            if ($splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement);
                }

                if ($currentStatement->tokenCount() === 0 && $currentStatement->next()->firstToken()->is([T_ATTRIBUTE, T_COMMENT, T_DOC_COMMENT])) {
                    $currentStatement->blankLineAfter = true;
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

        $statement->block->insertStatementAfter($finalStatement, $statement);

        for ($i = $lastIndex; $i >= $closerIndex; --$i) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);
            $finalStatement->prependToken($token);
        }
    }

    private function startNewStatement(Statement $statement): Statement
    {
        $newStatement = new Statement($statement->block);
        $newStatement->rootStatement = $statement;

        $statement->block->insertStatementAfter($newStatement, $statement);
        $newStatement->additionalDepth++;

        if ($statement->blankLineAfter) {
            $newStatement->blankLineAfter();
            $statement->blankLineAfter(false);
        }

        return $newStatement;
    }

    private function addBlankLineBefore(Statement $statement): void
    {
        $previousStatement = $statement->previous();

        if ($previousStatement && $statement->block === $previousStatement->block) {
            $previousStatement->blankLineAfter();
        }
    }
}
