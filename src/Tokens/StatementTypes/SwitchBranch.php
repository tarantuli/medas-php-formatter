<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class SwitchBranch implements StatementType
{
    public function __toString()
    {
        return 'switch branch';
    }
}
