<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter\Options;

readonly class Assessment
{
    public function __construct(
        public Option     $option,
        public array      $lengths,
        public float|null $quality,
    )
    {
    }
}
