<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\Contexts;

class GlobalScope implements Context
{
    public function __toString()
    {
        return 'G';
    }
}
