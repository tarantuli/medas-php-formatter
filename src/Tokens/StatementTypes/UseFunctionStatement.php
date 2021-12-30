<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class UseFunctionStatement implements StatementType
{
    public function __toString()
    {
        return 'use function';
    }
}
