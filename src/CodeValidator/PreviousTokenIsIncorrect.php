<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\CodeValidator;

use Medas\Core\Exceptions\BaseException;
use Medas\PhpTokenizer\Token;

class PreviousTokenIsIncorrect extends BaseException
{
    public function __construct(Token $token, Token $previous)
    {
        parent::__construct(
            $token->getTokenName(),
            $previous->getTokenName(),
            $token->previous->getTokenName()
        );
    }

    public function pattern(): string
    {
        return 'token %s should be preceded by %s, but is preceded by %s';
    }
}
