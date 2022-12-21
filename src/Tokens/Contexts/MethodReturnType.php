<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

class MethodReturnType implements Context
{
    public function __toString(): string
    {
        return 'MR';
    }
}
