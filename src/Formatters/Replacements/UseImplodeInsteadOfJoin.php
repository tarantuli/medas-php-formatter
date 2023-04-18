<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Tokens\StatementTypeFinder;
use Medas\PhpFormatter\Tokens\StatementTypes\GenericStatement;
use Medas\PhpFormatter\Tokens\Token;

#[Service]
class UseImplodeInsteadOfJoin extends BaseTokenReplacer
{
    public function __construct(
        private readonly StatementTypeFinder $typeFinder,
    )
    {
    }

    protected function doReplace(Token $token): bool
    {
        return $token->is(T_STRING)
            && $token->text === 'join'
            && $this->typeFinder->for($token->statement) instanceof GenericStatement;
    }

    protected function update(Token $token): void
    {
        $token->text = 'implode';
    }
}
