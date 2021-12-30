<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

class ClassDeclaration implements Context
{
    public function __toString()
    {
        return 'CD';
    }
}
