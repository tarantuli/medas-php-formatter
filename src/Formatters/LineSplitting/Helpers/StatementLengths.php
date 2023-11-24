<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Statement, TokenGroups};

#[Service]
readonly class StatementLengths
{
    private array $commentTokens;

    public function __construct(
        private TokenGroups $tokenGroups,
    )
    {
        $this->commentTokens = $this->tokenGroups->comments();
    }

    public function measure(Statement $statement): int
    {
        $length = $statement->block->depth * 4;
        $previousToken = null;
        $foundNonCommentToken = false;

        foreach ($statement as $token) {
            if (!$token->is($this->commentTokens) && !$token->inAttribute) {
                $foundNonCommentToken = true;
            }

            if ($foundNonCommentToken) {
                $length += strlen($token->text);

                if ($previousToken && $previousToken->spaceAfter) {
                    $length++;
                }
            }

            $previousToken = $token;
        }

        return $length;
    }
}
