<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class CannotRunCommandLineException extends BaseException
{
    public function getPattern(): string
    {
        return 'cannot run command line';
    }
}
