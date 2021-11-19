<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\TokenFormatters;

use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\TokenGroups;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequiredWhitespace implements TokenFormatter
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $spaceAroundRequired = $this->getSpaceAroundRequired();
        $spaceAroundNotNeeded = $this->getSpaceAroundNotNeeded();

        foreach ($tokens as $token) {
            if ($token->is($spaceAroundRequired) || $token->isTrueFalseNull()) {
                $token->spaceAfter = true;
            }

            if ($token->previous) {
                if (($token->is($spaceAroundRequired) || $token->isTrueFalseNull()) && !$token->previous->is($spaceAroundNotNeeded)) {
                    $token->previous->spaceAfter = true;
                }
                elseif ($token->is($spaceAroundNotNeeded)) {
                    $token->previous->spaceAfter = false;
                }
            }
        }
    }

    private function getSpaceAroundRequired(): array
    {
        return $this->tokenGroups->keywords();
    }

    private function getSpaceAroundNotNeeded(): array
    {
        return $this->tokenGroups->symbolOperators();
    }
}
