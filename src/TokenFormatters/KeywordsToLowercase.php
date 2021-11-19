<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\TokenFormatters;

use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\TokenGroups;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class KeywordsToLowercase implements TokenFormatter
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $reservedWords = $this->tokenGroups->texts();

        foreach ($tokens as $token) {
            if ($token->is($reservedWords) || $token->isTrueFalseNull()) {
                $token->text = strtolower($token->text);
            }
        }
    }
}
