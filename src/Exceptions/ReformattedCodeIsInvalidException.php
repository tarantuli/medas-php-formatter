<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class ReformattedCodeIsInvalidException extends BaseException
{

    public function __construct(string $code, string $errorMessage)
    {
        parent::__construct($code, $errorMessage);
    }

    public function getPattern(): string
    {
        return 'reformatted code "%s" is invalid: %s';
    }
}
