<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

class TokenCollection implements \Iterator, \Countable
{
    public Block $structure;
    /** @var Token[] */
    private array $tokens = [];
    private int $index = 0;
    private string $sourceHash;

    public function add(Token $token): void
    {
        $this->tokens[] = $token;
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

    public function sourceHash(): string
    {
        return $this->sourceHash;
    }

    public function setSourceHash(string $sourceHash): self
    {
        $this->sourceHash = $sourceHash;

        return $this;
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
