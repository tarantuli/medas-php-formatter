<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class ReturnStatement implements StatementType
{
    public function __toString()
    {
        return 'return statement';
    }
}
