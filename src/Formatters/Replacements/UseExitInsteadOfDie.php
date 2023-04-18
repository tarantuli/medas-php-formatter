<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Tokens\Token;

#[Service]
class UseExitInsteadOfDie extends BaseTokenReplacer
{
    protected function doReplace(Token $token): bool
    {
        // Both die() and exit() resolve to T_EXIT, but their text differs
        return $token->is(T_EXIT);
    }

    protected function update(Token $token): void
    {
        $token->text = 'exit';
    }
}
