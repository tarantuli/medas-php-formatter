<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\AsSingleton;
use Medas\Core\Interfaces\ConfigGroup;

class RootGroup implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return null;
    }

    public function name(): string
    {
        return 'php-formatter';
    }
}
