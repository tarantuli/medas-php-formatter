<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class BlankLine implements StatementType
{
    public function __toString()
    {
        return 'blank line';
    }
}
