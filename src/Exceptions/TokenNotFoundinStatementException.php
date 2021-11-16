<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\Token;

class TokenNotFoundinStatementException extends BaseException
{
    public function __construct(Token $token, Statement $statement)
    {
        parent::__construct($token, $statement);
    }

    public function getPattern(): string
    {
        return 'token %s not found in statement %s';
    }
}
