<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens\StatementTypes;

interface StatementType extends \Stringable
{
    public static function instance(): self;
}
