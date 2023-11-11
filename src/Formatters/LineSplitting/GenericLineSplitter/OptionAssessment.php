<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

readonly class OptionAssessment
{
    public function __construct(
        public Option     $option,
        public array      $lengths,
        public float|null $quality,
    )
    {
    }
}
