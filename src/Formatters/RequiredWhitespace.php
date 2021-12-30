<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Tokens\TokenGroups;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class RequiredWhitespace extends BaseFormatter
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

    public function format(TokenTree $tree): void
    {
        $spaceAroundRequired = $this->getSpaceAroundRequired();
        $spaceAroundNotNeeded = $this->getSpaceAroundNotNeeded();

        foreach ($tree as $token) {
            if ($token->is($spaceAroundRequired) || $token->isTrueFalseNull()) {
                $token->spaceAfter = true;
            }

            if ($token->previous) {
                if ($token->spaceAfter && !$token->previous->is($spaceAroundNotNeeded)) {
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
