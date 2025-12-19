<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\CodeValidator;

use Medas\Core\Exceptions\BaseException;
use Medas\PhpTokenizer\Token;

class NextTokenIsIncorrect extends BaseException
{
    public function __construct(Token $token, Token $next)
    {
        parent::__construct(
            $token->getTokenName(),
            $next->getTokenName(),
            $token->next->getTokenName()
        );
    }

    public function pattern(): string
    {
        return 'token %s should be followed by %s, but is followed by %s';
    }
}
