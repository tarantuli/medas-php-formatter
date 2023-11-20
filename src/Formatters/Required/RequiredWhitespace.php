<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Required;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\TokenGroups;

#[Service]
readonly class RequiredWhitespace extends BaseFormatter
{
    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
    }

    public function priority(): int
    {
        return 1700;
    }

    public function format(Job $job): void
    {
        $spaceAroundRequired = $this->getSpaceAroundRequired();
        $spaceAroundNotNeeded = $this->getSpaceAroundNotNeeded();

        foreach ($job->tree as $token) {
            if ($token->is($spaceAroundRequired) || $token->isTrueFalseNull()) {
                $token->spaceAfter();
            }

            if ($token->previous) {
                if ($token->spaceAfter && !$token->previous->is($spaceAroundNotNeeded)) {
                    $token->previous->spaceAfter();
                }
                elseif ($token->is($spaceAroundNotNeeded)) {
                    $token->previous->spaceAfter(false);
                }
            }

            if ($token->is(T_COMMENT)) {
                $token->lineBreakAfter();
            }
        }
    }

    private function getSpaceAroundRequired(): array
    {
        return $this->tokenGroups->keywords();
    }

    private function getSpaceAroundNotNeeded(): array
    {
        return array_merge($this->tokenGroups->symbolOperators(), $this->tokenGroups->brackets(), [T_PIPE]);
    }
}
