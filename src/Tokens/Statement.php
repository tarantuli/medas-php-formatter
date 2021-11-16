<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Exceptions\TokenNotFoundinStatementException;

class Statement implements \IteratorAggregate
{
    /** @var Token[] */
    private array $tokens = [];

    public function __construct(public Block $block)
    {
    }

    public function appendToken(Token $token): void
    {
        $this->tokens[] = $token;

        $token->block = $this->block;
        $token->statement = $this;
    }

    /**
     * @return \Generator|Token[]
     * @noinspection PhpDocSignatureInspection
     */
    public function getIterator(): \Generator
    {
        yield from $this->tokens;
    }

    public function removeToken(Token $token): void
    {
        if (false === $i = array_search($token, $this->tokens)) {
            throw new TokenNotFoundinStatementException($token, $this);
        }

        unset($this->tokens[$i]);
        $this->tokens = array_values($this->tokens);
    }

    public function insertTokenAfter(Token $after, Token $token): void
    {
        if (false === $i = array_search($after, $this->tokens)) {
            throw new TokenNotFoundinStatementException($token, $this);
        }

        $token->block = $after->block;
        $token->statement = $after->statement;
        $token->inString = $after->inString;
        $token->inAttribute = $after->inAttribute;

        array_splice($this->tokens, $i + 1, 0, [$token]);
        $this->tokens = array_values($this->tokens);
    }

    public function getLastToken(): Token|null
    {
        return $this->tokens[count($this->tokens) - 1] ?? null;
    }

    public function getTokenAfter(Token $token): Token|null
    {
        if (false === $i = array_search($token, $this->tokens)) {
            throw new TokenNotFoundinStatementException($token, $this);
        }

        return $this->tokens[$i + 1] ?? null;
    }
}
