<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Tokens\{TokenGroups, TokenTree};

#[Service]
class KeywordsToLowercase extends BaseFormatter
{
    public function __construct(
        private readonly TokenGroups $tokenGroups,
    )
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
