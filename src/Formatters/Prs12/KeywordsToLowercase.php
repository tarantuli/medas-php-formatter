<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\TokenGroups;

#[Service]
readonly class KeywordsToLowercase extends BaseFormatter
{
    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
    }

    public function format(Job $job): void
    {
        $reservedWords = $this->tokenGroups->texts();

        foreach ($job->tree as $token) {
            if ($token->is($reservedWords) || $token->isTrueFalseNull()) {
                $token->text = strtolower($token->text);
            }
        }
    }
}
