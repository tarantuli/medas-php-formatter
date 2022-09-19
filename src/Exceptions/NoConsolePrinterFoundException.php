<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class NoConsolePrinterFoundException extends BaseException
{
    public function pattern(): string
    {
        return 'no console printer registered. Perhaps use morphp/medas-console-printer?';
    }
}
