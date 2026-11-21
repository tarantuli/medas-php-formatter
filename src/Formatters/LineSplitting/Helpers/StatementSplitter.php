<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Tokens;
use Medas\PhpTokenizer\{Statement, Token, TokenGroups, TokenRole};

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

        if ($statement->lastToken()->is(Tokens::CLOSING_BRACKETS)
                && !$this->opensInRegion($statement, $statement->lastToken(), $openerIndex)) {
            // Statements that end with closing brackets are hard to process correctly elsewhere,
            // so skip the last token here. Not one that closes a bracket in the region, like the "]" of
            // "fn() => ['name' => $name]" as the last argument.
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

        $regionDepth = $this->separatorDepth($statement, $separators, $openerIndex, $closerIndex);

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            if (!$splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);
                }

                $currentStatement->prependToken($token);
            }

            $isSeparator = $this->isSeparator($token, $separators) && $token->bracketDepth === $regionDepth;

            if ($isSeparator && $i > $openerIndex + 1) {
                $currentStatement = $this->startNewStatement($statement, $additionalDepth);
            }

            if ($token->closesAttribute) {
                $token->lineBreakAfter();
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

    private function opensInRegion(Statement $statement, Token $closer, int $openerIndex): bool
    {
        $opener = $closer->matchingBracket;

        return $opener?->statement === $statement && $statement->getIndex($opener) > $openerIndex;
    }

    /**
     * The colon of "?:" is no separator, even when colons are.
     */
    private function isSeparator(Token $token, array $separators): bool
    {
        return $token->is($separators) && $token->role !== TokenRole::ShortTernary;
    }

    /**
     * The bracket depth at which the region between the opener and the closer is split: that of its outermost
     * separators. Separators inside brackets in the region, like the commas of a nested call, are deeper.
     */
    private function separatorDepth(
        Statement $statement,
        array     $separators,
        int       $openerIndex,
        int       $closerIndex
    ): int|null
    {
        $depth = null;

        for ($i = $openerIndex + 1; $i < $closerIndex; ++$i) {
            $token = $statement->getToken($i);

            if ($this->isSeparator($token, $separators)) {
                $depth = $depth === null ? $token->bracketDepth : min($depth, $token->bracketDepth);
            }
        }

        return $depth;
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
}
