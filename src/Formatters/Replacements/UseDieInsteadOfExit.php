<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Token;

#[Service]
readonly class UseDieInsteadOfExit extends BaseTokenReplacer
{
    protected function doReplace(Token $token): bool
    {
        // Both die() and exit() resolve to T_EXIT, but their text differs
        return $token->is(T_EXIT) && $token->text === 'exit';
    }

    protected function update(Token $token): void
    {
        $token->text = 'die';
    }
}
