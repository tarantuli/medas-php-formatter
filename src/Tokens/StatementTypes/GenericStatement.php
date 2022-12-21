<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class GenericStatement implements StatementType
{
    public function __toString(): string
    {
        return '';
    }
}
