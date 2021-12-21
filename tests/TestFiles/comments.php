<?php

// Comment flush left
class A
{
    /** @var Type[]  */
    private array $int;

    /**
     * Doccomment for method startsWithDoccomment()
     */
    public function startsWithDoccomment(string $c, int $d): int
    {
        // Comment in a method
        return strlen($c) + /** Inline doccomment */ $d;
    }

    public function doesntStartWithDoccomment(): int
    {
        return 1;
    }
}
