<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

use Medas\Core\AsSingleton;

class GlobalScope implements Context
{
    use AsSingleton;

    public function __toString(): string
    {
        return 'G';
    }
}
