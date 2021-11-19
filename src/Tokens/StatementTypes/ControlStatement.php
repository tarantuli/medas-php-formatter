<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class ControlStatement implements StatementType
{
    public function __toString()
    {
        return 'control statement';
    }
}
