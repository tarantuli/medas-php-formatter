<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Token;

#[Service]
readonly class ReturnTypeTokens
{
    public function isReturnTypeToken(Token $token): bool
    {
        $previousToken = $token;

        while ($previousToken = $previousToken->previous) {
            if ($previousToken->is(T_ROUND_BRACKET_CLOSE)) {
                return true;
            }

            if ($previousToken->is([T_STRING, T_COLON, T_PIPE, T_NAME_FULLY_QUALIFIED])) {
                continue;
            }

            return false;
        }

        return false;
    }
}
