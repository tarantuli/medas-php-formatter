<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

class DeclareStatement implements StatementType
{
    public function __toString()
    {
        return 'declare statement';
    }
}
