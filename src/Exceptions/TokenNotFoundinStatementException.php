<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Exceptions;

use Medas\Core\Exceptions\BaseException;
use Medas\PhpFormatter\Tokens\Statement;
use Medas\PhpFormatter\Tokens\Token;

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
