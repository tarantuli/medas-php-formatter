<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class UseTraitStatement implements StatementType
{
    public function __toString(): string
    {
        return 'use trait';
    }
}
