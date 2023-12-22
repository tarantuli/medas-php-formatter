<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preformatters;

use Medas\PhpFormatter\Job;

interface Preformatter
{
    public function preformat(Job $job): void;

    public function additionalFormatters(): array;
}
