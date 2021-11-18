<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class UseConstStatement implements StatementType
{
    public function __toString()
    {
        return 'use const';
    }
}
