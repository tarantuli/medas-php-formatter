<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class SourceCodeIsInvalidException extends BaseException
{
    public function __construct(string $code, string $errorMessage)
    {
        $logfile = 'invalid-source-code.php';

        if (is_writable($logfile)) {
            file_put_contents($logfile, $code);

            parent::__construct($errorMessage, $logfile);
        }
        else {
            parent::__construct($errorMessage, $code);
        }
    }

    public function pattern(): string
    {
        return 'source code is invalid: %s (see %s)';
    }
}
