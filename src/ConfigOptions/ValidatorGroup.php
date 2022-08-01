<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\ConfigOptions\ConfigGroup;
use Medas\ServiceManager\AsSingleton;

class ValidatorGroup implements ConfigGroup
{
    use AsSingleton;

    public function parent(): ConfigGroup|null
    {
        return RootGroup::instance();
    }

    public function name(): string
    {
        return 'validator';
    }
}
