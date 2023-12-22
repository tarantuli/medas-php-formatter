<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

class ReorderingJob
{
    public int $declarationCount = 0;
    public int $elementIndex = 0;
    public int $currentSection = 0;
    public int $currentBraceDepth = 0;
    public int $currentParenthesisDepth = 0;
    public bool $inClassCode = false;
    public bool $inTrailingCode = false;
    public bool $inStatement = false;
    public bool $inString = false;

    // Property variables
    public int|null $classBraceDepth = null;
    public int|null $classParenthesisDepth = null;
    public int|null $classOpeningBraceAtParenthesisDepth = null;

    // Results
    public string $leadingCode = '';

    /** @var Element[] */
    public array $elements = [];

    /** @var Element[] */
    public array $unsortedElements = [];

    public string $trailingCode = '';

    public function __construct(
        /** @var \PhpToken[] */
        public array $tokens,
    )
    {
        $this->elements[0] = new Element();
    }
}
