<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ReformattedCodeIsInvalidException extends BaseException
{

    public function __construct(string $code, string $errorMessage)
    {
        $logfile = realpath(__DIR__ . '/../../var/logs') . DIRECTORY_SEPARATOR  . 'invalid-code.php';
        file_put_contents($logfile, $code);
        parent::__construct($logfile, $errorMessage);
    }

    public function getPattern(): string
    {
        return 'reformatted code in %s is invalid: %s';
    }
}
