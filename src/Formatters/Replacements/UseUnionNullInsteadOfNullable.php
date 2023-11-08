<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

/**
 * Use "type|null" instead of "?type" in property, parameter and return type definitions
 */
#[Service]
readonly class UseUnionNullInsteadOfNullable extends BaseFormatter
{
    public function format(Job $job): void
    {
        // TODO: Implement format() method.
    }
}
