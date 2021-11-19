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

    public function mergeWithPrevious(Statement $secondStatement): void
    {
        $second = array_search($secondStatement, $this->elements, true);
        $first = $second - 1;
        $firstStatement = $this->elements[$first];

        foreach ($secondStatement as $token) {
            $firstStatement->appendToken($token);
        }

        $firstStatement->blankLineAfter = $secondStatement->blankLineAfter;

        array_splice($this->elements, $second, 1);
        unset($secondStatement);
    }

    /**
     * @return \Generator|Statement[]|Block[]
     * @noinspection PhpDocSignatureInspection
     */
    public function getIterator(): \Generator
    {
        yield from $this->elements;
    }

    public function lastStatement(): Statement
    {
        return $this->elements[count($this->elements) - 1];
    }

    public function __debugInfo()
    {
        $elements = [];

        foreach ($this->elements as $element) {
            $elements[] = sprintf('%s', $element::class);
        }

        return $elements;
    }
}
