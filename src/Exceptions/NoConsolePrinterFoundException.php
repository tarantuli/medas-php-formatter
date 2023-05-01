<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\Core\Exceptions\Suggestions;

class NoConsolePrinterFoundException extends BaseException implements Suggestions
{
    public function pattern(): string
    {
        return 'no console printer registered';
    }

    public function suggestions(): array
    {
        return [
            'you can use morphp/medas-console-printer',
        ];
    }
}
