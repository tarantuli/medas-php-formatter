<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Tokens\Contexts\GlobalScope;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class StructureFinder
{
    private Block $block;
    private Statement $statement;
    private int $depth;
    private bool $inString;
    private bool $inAttribute;
    private Contexts\Context $context;

    private array $openBlocks = [];

    private bool $ignoreNextDoubleQuote = false;
    private bool $startNewStatementBeforeNext = false;
    private bool $nextBraceOpensForClause = false;
    private int $forClauseDepth = 0;

    public function determine(TokenCollection $tokens): Block
    {
        $this->depth = 0;
        $this->inString = false;
        $this->inAttribute = false;
        $this->context = new GlobalScope();

        $document = new Block($this->depth, null);
        $this->block = $document;
        $this->statement = $this->block->appendNewStatement();

        foreach ($tokens as $token) {
            $this->process($token);
        }

        return $document;
    }

    private function process(Token $token): void
    {
        if ($token->is(T_CURLY_BRACKET_CLOSE)) {
            // The previous block is closed, return to the last open block
            $this->block = array_pop($this->openBlocks);
            $this->statement = $this->block->appendNewStatement();
            $this->startNewStatementBeforeNext = false;
            --$this->depth;
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
        $token->context = $this->context;

        $this->statement->appendToken($token);

        if ($token->is([T_OPEN_TAG, T_CURLY_BRACKET_CLOSE])) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
        }

        if ($token->is(T_SEMICOLON) && !$this->forClauseDepth) {
            // Next token starts on a new line
            $this->startNewStatementBeforeNext = true;
        }

        if ($token->is(T_CURLY_BRACKET_OPEN)) {
            // Store the current open block
            $this->openBlocks[] = $this->block;

            // Next token starts in a new block
            $newBlock = new Block(++$this->depth, $this->statement);
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
    }
}
