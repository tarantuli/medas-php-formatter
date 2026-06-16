<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Interfaces\DeclaresPriority;
use Medas\PhpFormatter\Job;

interface Formatter extends DeclaresPriority
{
    public function format(Job $job): void;

    public function additionalFormatters(): array;

    /**
     * Formatters with higher priority values are called first, lower values are called later.
     */
    public function priority(): int;
}
