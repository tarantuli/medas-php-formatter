<?php

namespace App;

use Medas\Core\Attributes;

#[Attributes\Service]
readonly class Qualified
{
    public function __construct(
        private Zebra $zebra,
        private Apple $apple,
    )
    {
    }
}
