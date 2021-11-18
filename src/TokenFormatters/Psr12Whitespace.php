<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\TokenFormatters;

use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12Whitespace implements TokenFormatter
{
    public function format(TokenCollection $tokens): void
    {
        foreach ($tokens as $token) {
            if ($token->is(T_CURLY_BRACKET_OPEN) && $token->previous->is(T_ROUND_BRACKET_CLOSE)) {
                $token->previous->spaceAfter = true;
            }
        }
    }
}
