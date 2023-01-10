<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\Contexts;

interface Context extends \Stringable
{
    public static function instance(): self;
}
