<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

class MethodParameters implements Context
{
    public function __toString(): string
    {
        return 'MP';
    }
}
