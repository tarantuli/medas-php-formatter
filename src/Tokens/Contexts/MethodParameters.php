<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

use Medas\ServiceManager\AsSingleton;

class MethodParameters implements Context
{
    use AsSingleton;

    public function __toString()
    {
        return 'MP';
    }
}
