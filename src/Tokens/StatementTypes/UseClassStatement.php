<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class UseClassStatement implements StatementType
{
    public function __toString()
    {
        return 'use class';
    }
}
