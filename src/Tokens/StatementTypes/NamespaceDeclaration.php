<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class NamespaceDeclaration implements StatementType
{
    public function __toString()
    {
        return 'namespace declaration';
    }
}
