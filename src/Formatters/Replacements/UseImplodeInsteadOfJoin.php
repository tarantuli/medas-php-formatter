<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Replacements;

use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\GenericStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class UseImplodeInsteadOfJoin extends BaseTokenReplacer
{
    public function __construct(private StatementTypeFinder $typeFinder)
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
