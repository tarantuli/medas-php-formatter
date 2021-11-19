<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class ClassPropertyDeclaration implements StatementType
{
    public function __toString()
    {
        return 'class property declaration';
    }
}
