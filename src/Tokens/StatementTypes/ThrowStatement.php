<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

use Medas\ServiceManager\AsSingleton;

class ThrowStatement implements StatementType
{
    use AsSingleton;

    public function __toString()
    {
        return 'throw statement';
    }
}
