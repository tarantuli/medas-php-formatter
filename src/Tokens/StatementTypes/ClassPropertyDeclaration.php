<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class ClassPropertyDeclaration implements StatementType
{
    public function __toString(): string
    {
        return 'class property declaration';
    }
}
