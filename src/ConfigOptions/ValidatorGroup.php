<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\ConfigOptions;

use Medas\Core\Attributes\Service;
use Medas\Core\Interfaces\ConfigGroup;

#[Service]
class ValidatorGroup implements ConfigGroup
{
    public function __construct(
        private readonly RootGroup $group,
    )
    {
    }

    public function parent(): ConfigGroup|null
    {
        return $this->group;
    }

    public function name(): string
    {
        return 'validator';
    }
}
