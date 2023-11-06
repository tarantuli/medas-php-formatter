<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\GenericLineSplitter;

readonly class Cluster
{
    public function __construct(
        public int $id,
        public int $openerIndex,
    )
    {
    }
}
