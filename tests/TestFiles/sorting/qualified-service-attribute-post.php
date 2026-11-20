<?php

namespace App;

use Medas\Core\Attributes;

#[Attributes\Service]
readonly class Qualified
{
    public function __construct(
        private Apple $apple,
        private Zebra $zebra,
    )
    {
    }
}
