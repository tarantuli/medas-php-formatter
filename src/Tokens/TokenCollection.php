<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

class TokenCollection implements \Iterator, \Countable
{
    /** @var Token[] */
    private array $tokens = [];
    private int $index = 0;

    public function __construct(string $code)
    {
        $phpTokens = \PhpToken::tokenize($code, TOKEN_PARSE);
        $previousToken = null;

        foreach ($phpTokens as $phpToken) {
            $token = $this->fromPhpToken($phpToken);

            if ($previousToken) {
                $token->previous = $previousToken;
                $previousToken->next = $token;
            }

            $this->tokens[] = $token;
            $previousToken = $token;
        }
    }

    private function fromPhpToken(\PhpToken $phpToken): Token
    {
        if ($phpToken->is(T_OPEN_TAG)) {
            $phpToken->text = rtrim($phpToken->text);
        }

        return new Token($phpToken->id, $phpToken->text, $phpToken->line, $phpToken->pos);
    }

    public function removeByType(int|string|array $kind)
    {
        foreach ($this as $i => $token) {
            if ($token->is($kind)) {
                $this->remove($i);
            }
        }
    }

    public function remove(int $index)
    {
        array_splice($this->tokens, $index, 1);

        if (array_key_exists($index - 1, $this->tokens)) {
            if (array_key_exists($index, $this->tokens)) {
                $this->tokens[$index - 1]->next = $this->tokens[$index];
                $this->tokens[$index]->previous = $this->tokens[$index - 1];
            }
            else {
                $this->tokens[$index - 1]->next = null;
            }
        }
        else {
            if (array_key_exists($index, $this->tokens)) {
                $this->tokens[$index]->previous = null;
            }
        }

        if ($index <= $this->index) {
            --$this->index;
        }
    }

    public function current(): Token
    {
        return $this->tokens[$this->index];
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return array_key_exists($this->index, $this->tokens);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
