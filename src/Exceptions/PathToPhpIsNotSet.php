<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class PathToPhpIsNotSet extends BaseException
{
    public function pattern(): string
    {
        return 'Path to PHP is not set, see ConfigOption PathToPhp';
    }
}
