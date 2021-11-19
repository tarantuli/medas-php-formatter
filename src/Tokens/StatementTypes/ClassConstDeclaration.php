<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class ClassConstDeclaration implements StatementType
{
    public function __toString()
    {
        return 'class const declaration';
    }
}
