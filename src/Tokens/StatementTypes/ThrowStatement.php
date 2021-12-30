<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class ThrowStatement implements StatementType
{
    public function __toString()
    {
        return 'throw statement';
    }
}
