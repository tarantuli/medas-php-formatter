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
        $arrowFunctionsByArrow = $this->arrowFunctionsByArrow($statement);

        /** @var Statement[] $segments */
        $segments = [];

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            if (!$splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);

                    array_unshift($segments, $currentStatement);
                }

                $currentStatement->prependToken($token);
            }

            $isSeparator = $this->isSeparator($token, $separators)
                && $token->bracketDepth === $regionDepth;

            if ($isSeparator && $i > $openerIndex + 1) {
                $currentStatement = $this->startNewStatement($statement, $additionalDepth);

                array_unshift($segments, $currentStatement);
            }

            if ($token->closesAttribute) {
                $token->lineBreakAfter();
            }

            if ($splitAfter) {
                if ($currentStatement === null) {
                    $currentStatement = $this->startNewStatement($statement, $additionalDepth);

                    array_unshift($segments, $currentStatement);
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

        $this->indentArrowFunctionBodies($statement, $segments, $arrowFunctionsByArrow);
    }

    /**
     * The fn tokens of the statement's arrow functions, keyed by the object id of their => token.
     *
     * @return Token[]
     */
    private function arrowFunctionsByArrow(Statement $statement): array
    {
        $arrowFunctionsByArrow = [];

        // The fn tokens still waiting for their =>, innermost last
        $openArrowFunctions = [];

        foreach ($statement as $token) {
            if ($token->is(T_FN)) {
                $openArrowFunctions[] = $token;
            }
            elseif ($token->is(T_DOUBLE_ARROW)
                    && $openArrowFunctions
                    && end($openArrowFunctions)->bracketDepth === $token->bracketDepth) {
                $arrowFunctionsByArrow[spl_object_id($token)] = array_pop($openArrowFunctions);
            }
        }

        return $arrowFunctionsByArrow;
    }

    /**
     * The body of an arrow function that starts on a split-off line belongs to that line, so it goes one level
     * deeper than it, like the body after the second => of "'key' => fn() => $body".
     *
     * @param Statement[] $segments
     * @param Token[]     $arrowFunctionsByArrow
     */
    private function indentArrowFunctionBodies(
        Statement $statement,
        array     $segments,
        array     $arrowFunctionsByArrow
    ): void
    {
        $extraDepth = 0;

        foreach ($segments as $segment) {
            $firstToken = $segment->firstToken();
            $arrowFunction = $firstToken ? $arrowFunctionsByArrow[spl_object_id($firstToken)] ?? null : null;

            // An arrow function that starts in the root statement keeps its body at the normal depth
            if ($arrowFunction !== null && $arrowFunction->statement !== $statement) {
                ++$extraDepth;
            }

            $segment->additionalDepth += $extraDepth;
        }
    }

    private function opensInRegion(Statement $statement, Token $closer, int $openerIndex): bool
    {
        $opener = $closer->matchingBracket;

        return $opener?->statement === $statement && $statement->getIndex($opener) > $openerIndex;
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

    /**
     * The colon of "?:" is no separator, even when colons are.
     */
    private function isSeparator(Token $token, array $separators): bool
    {
        return $token->is($separators) && $token->role !== TokenRole::ShortTernary;
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
