<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\ServiceManager\AsSingleton;
use Medas\ServiceManager\ConfigOptions\ConfigGroup;
use Medas\ServiceManager\ConfigOptions\ConfigOption;

class PathToPhp implements ConfigOption
{
    use AsSingleton;

    public function group(): ConfigGroup
    {
        return ValidatorGroup::instance();
    }

    public function name(): string
    {
        return 'path-to-php';
    }

    public function description(): string
    {
        return 'The path to the php executable';
    }

    public function isValid(mixed $value): bool
    {
        return is_string($value) && file_exists($value);
    }

    public function hasDefault(): bool
    {
        return false;
    }

    public function default(): mixed
    {
        return null;
    }
}
