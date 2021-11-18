<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

class Block implements \IteratorAggregate
{
    /** @var Statement[]|Block[] */
    private array $elements = [];

    public function __construct(public int $depth, public Statement|null $opener)
    {
    }

    public function appendNewStatement(): Statement
    {
        return $this->elements[] = new Statement($this);
    }

    public function appendBlock(Block $block): void
    {
        $this->elements[] = $block;
    }

    /**
     * @return \Generator|Statement[]|Block[]
     * @noinspection PhpDocSignatureInspection
     */
    public function getIterator(): \Generator
    {
        yield from $this->elements;
    }
}
