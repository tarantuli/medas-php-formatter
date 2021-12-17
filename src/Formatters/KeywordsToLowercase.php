<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\TokenGroups;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class KeywordsToLowercase extends BaseFormatter
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

    public function format(TokenTree $tree): void
    {
        $reservedWords = $this->tokenGroups->texts();

        foreach ($tree as $token) {
            if ($token->is($reservedWords) || $token->isTrueFalseNull()) {
                $token->text = strtolower($token->text);
            }
        }
    }
}
