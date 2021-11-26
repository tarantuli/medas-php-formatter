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

    public function getPreviousStatement(Statement $statement): ?Statement
    {
        $index = $this->getIndex($statement);
        return $this->elements[$index - 1] ?? null;
    }

    private function getIndex(Statement $statement): int|null
    {
        $index = array_search($statement, $this->elements, true);

        return false === $index ? null : $index;
    }

    public function getNextStatement(Statement $statement): ?Statement
    {
        $index = $this->getIndex($statement);
        return $this->elements[$index + 1] ?? null;
    }

    public function mergeWithPrevious(Statement $secondStatement): void

    {
        $second = $this->getIndex($secondStatement);
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
     * foreach ($block) only returns the statements in this block, recursively! Be careful to use $statement->block
     * and not $block itself.
     *
     * @return \Generator|Statement[]
     * @noinspection PhpDocSignatureInspection
     */
    public function getIterator(): \Generator
    {
        foreach ($this->elements as $element) {
            if ($element instanceof Block) {
                yield from $element;
            }
            else {
                yield $element;
            }
        }
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

    public function deleteStatement(Statement $statement): void
    {
        $index = $this->getIndex($statement);
        array_splice($this->elements, $index, 1);
        unset($statement);
    }
}
