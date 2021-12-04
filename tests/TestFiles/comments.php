<?php

// Comment flush left
class A
{
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
