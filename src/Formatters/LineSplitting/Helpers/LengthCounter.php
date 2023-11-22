<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting\Helpers;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class LengthCounter
{
    public function count(Statement $statement): int
    {
        $length = $statement->block->depth * 4;
        $previousToken = null;
        $foundNonCommentToken = false;

        foreach ($statement as $token) {
            if (!$token->is([T_COMMENT, T_DOC_COMMENT, T_ATTRIBUTE]) && !$token->inAttribute) {
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
