<?php

namespace App;

#[Tag(Service)]
class NotAService
{
    public function __construct(
        private Zebra $zebra,
        private Apple $apple,
    )
    {
    }

    public function make(): Join
    {
        return new Join(join(',', []));
    }
}
