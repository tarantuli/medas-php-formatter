<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

use Medas\ServiceManager\AsSingleton;

class ControlStatement implements StatementType
{
    use AsSingleton;

    public function __toString(): string
    {
        return 'control statement';
    }
}
