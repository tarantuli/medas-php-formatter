<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\StatementTypes;

class BlockCloser implements StatementType
{
    public function __toString()
    {
        return '}';
    }
}
