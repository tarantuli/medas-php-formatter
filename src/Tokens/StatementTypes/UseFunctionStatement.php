<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

use Medas\ServiceManager\AsSingleton;

class UseFunctionStatement implements StatementType
{
    use AsSingleton;

    public function __toString(): string
    {
        return 'use function';
    }
}
