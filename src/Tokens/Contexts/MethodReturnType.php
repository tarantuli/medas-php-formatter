<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\Contexts;

class MethodReturnType implements Context
{
    public function __toString()
    {
        return 'MR';
    }
}
