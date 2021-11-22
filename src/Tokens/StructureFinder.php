<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class StructureFinder
{
    private Block $block;
    private Statement $statement;
    private int $blockDepth;
    private bool $inString;
    private bool $inAttribute;

    private array $openBlocks = [];

    private bool $ignoreNextDoubleQuote;
    private bool $startNewStatementBeforeNext;
    private bool $nextBraceOpensForClause;
    private int $forClauseDepth;

    private int $matchClauseDepth;
    private bool $nextCommaEndsStatement;
    private bool $nextBraceOpensMatchClause;

    public function determine(TokenCollection $tokens): Block
    {
        $this->reset();

        $document = new Block($this->blockDepth, null);

        $this->block = $document;
        $this->statement = $this->block->appendNewStatement();

        foreach ($tokens as $token) {
            $this->process($token);
        }

        return $document;
    }

    private function reset(): void
    {
        $this->blockDepth = 0;
        $this->inString = false;
        $this->inAttribute = false;

        $this->openBlocks = [];

        $this->ignoreNextDoubleQuote = false;
        $this->startNewStatementBeforeNext = false;
        $this->nextBraceOpensForClause = false;
        $this->forClauseDepth = 0;

        $this->matchClauseDepth = 0;
        $this->nextBraceOpensMatchClause = false;
        $this->nextCommaEndsStatement = false;
    }

    private function process(Token $token): void
    {
        if ($token->is(T_CURLY_BRACKET_CLOSE)) {
            // Delete the last statement if it's empty
            if (null === $this->statement->firstToken()) {
                $this->block->deleteStatement($this->statement);
            }

            // The previous block is closed, return to the last open block
            $this->block = array_pop($this->openBlocks);
            $this->statement = $this->block->appendNewStatement();
            $this->startNewStatementBeforeNext = false;
            --$this->blockDepth;
        }

        if ($this->startNewStatementBeforeNext) {
            $this->statement = $this->block->appendNewStatement();
            $this->startNewStatementBeforeNext = false;
        }

        if ($this->inAttribute && $token->is(T_SQUARE_BRACKET_CLOSE)) {
            // This token closes an attribute
            $this->inAttribute = false;
            $this->startNewStatementBeforeNext = true;
        }

        if ($this->inString && $token->is(T_DOUBLE_QUOTE)) {
            // This token closes a string
            $this->inString = false;
            $this->ignoreNextDoubleQuote = true;
        }

        $token->block = $this->block;
        $token->statement = $this->statement;
        $token->inString = $this->inString;
        $token->inAttribute = $this->inAttribute;

        $this->statement->appendToken($token);

        if ($token->is([T_OPEN_TAG, T_COMMENT])) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
        }

        if ($token->is(T_SEMICOLON) && !$this->forClauseDepth) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
        }

        if ($token->is(T_CURLY_BRACKET_CLOSE) && !$this->matchClauseDepth) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
        }

        if ($token->is(T_DOUBLE_ARROW) && $this->matchClauseDepth) {
            $this->nextCommaEndsStatement = true;
        }

        if ($token->is(T_COMMA) && $this->nextCommaEndsStatement) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
            $this->nextCommaEndsStatement = false;
        }

        if ($token->is(T_CURLY_BRACKET_OPEN)) {
            // Store the current open block
            $this->openBlocks[] = $this->block;

            // Next token starts in a new block
            $newBlock = new Block(++$this->blockDepth, $this->statement);
            $this->block->appendBlock($newBlock);
            $this->block = $newBlock;
            $this->statement = $this->block->appendNewStatement();
        }

        if ($token->is(T_ATTRIBUTE)) {
            // Next token is in an attribute
            $this->inAttribute = true;
        }

        if ($token->is(T_DOUBLE_QUOTE)) {
            if ($this->ignoreNextDoubleQuote) {
                // This double quote _closed_ a string already
                $this->ignoreNextDoubleQuote = false;
            }
            else {
                // Next token is in a string
                $this->inString = true;
            }
        }

        if ($token->is(T_FOR)) {
            $this->nextBraceOpensForClause = true;
        }

        if ($token->is(T_ROUND_BRACKET_OPEN) && ($this->forClauseDepth || $this->nextBraceOpensForClause)) {
            $this->nextBraceOpensForClause = false;
            ++$this->forClauseDepth;
        }

        if ($token->is(T_ROUND_BRACKET_CLOSE) && $this->forClauseDepth) {
            --$this->forClauseDepth;
        }

        if ($token->is(T_MATCH)) {
            $this->nextBraceOpensMatchClause = true;
        }

        if ($token->is(T_CURLY_BRACKET_OPEN) && ($this->matchClauseDepth || $this->nextBraceOpensMatchClause)) {
            $this->nextBraceOpensMatchClause = false;
            ++$this->matchClauseDepth;
        }

        if ($token->is(T_CURLY_BRACKET_CLOSE) && $this->matchClauseDepth) {
            --$this->matchClauseDepth;
        }
    }
}
