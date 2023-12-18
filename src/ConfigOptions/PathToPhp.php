<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\{Attributes\Service, Interfaces\ConfigGroup, Interfaces\ConfigOption};

#[Service]
readonly class PathToPhp implements ConfigOption
{
    public function __construct(
        private ValidatorGroup $group,
    )
    {
    }

    public function group(): ConfigGroup
    {
        return $this->group;
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
        return true;
    }

    public function default(): null
    {
        return null;
    }
}
