<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

use Medas\ServiceManager\AsSingleton;

class MethodReturnType implements Context
{
    use AsSingleton;

    public function __toString(): string
    {
        return 'MR';
    }
}
