<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class ClassDeclaration implements StatementType
{
    public function __toString(): string
    {
        return 'class declaration';
    }
}
