<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers;

use Medas\PhpFormatter\Job;

interface Preparser
{
    public function preparse(Job $job): void;
}
