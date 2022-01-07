<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

use Medas\ServiceManager\AsSingleton;

class GlobalScope implements Context
{
    use AsSingleton;

    public function __toString()
    {
        return 'G';
    }
}
