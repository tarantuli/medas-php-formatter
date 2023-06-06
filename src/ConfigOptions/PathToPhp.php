<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\{ConfigGroup, ConfigOption};

#[Service]
class PathToPhp implements ConfigOption
{
    public function __construct(
        private readonly ValidatorGroup $group,
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
        return false;
    }

    public function default(): mixed
    {
        return null;
    }
}
