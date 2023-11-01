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

    #[\AnAttribute]
    // Single-line comment
    /*
     * Multi-line comment
     */
    /**
     * Doccomment
     */
    public function startsWithThreeCommentTypes(): void
    {
        // Do nothing
    }

    public function doesntStartWithDoccomment(): int
    {
        /** @var  Interfaces\FullTrackInterface[] $tracks */
        foreach ($tracks as $track) {
            /** @noinspection PhpDynamicFieldDeclarationInspection */
            $track->distance = $this->determineDistance($track, $artist, $title);
        }

        return 1;
    }
}
