<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ElementSorter
{
    public function sort(ReorderingJob $job): void
    {
        usort($job->elements, fn(Element $a, Element $b) => $a->sortingKey <=> $b->sortingKey);
    }
}
