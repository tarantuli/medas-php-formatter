<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class DeclareStatement implements StatementType
{
    public function __toString()
    {
        return 'declare statement';
    }
}
