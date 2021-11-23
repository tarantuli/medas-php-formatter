<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\TokenReplacements;

use Medas\PhpBeautifier\Tokens\Token;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UseDieInsteadOfExit extends BaseTokenReplacer
{
    protected function doReplace(Token $token): bool
    {
        // Both die() and exit() resolve to T_EXIT, but their text differs
        return $token->is(T_EXIT);
    }

    protected function update(Token $token): void
    {
        $token->text = 'die';
    }
}
