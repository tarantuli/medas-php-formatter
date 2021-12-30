<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotRunCommandLineException extends BaseException
{
    public function getPattern(): string
    {
        return 'cannot run command line';
    }
}
