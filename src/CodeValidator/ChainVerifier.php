<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\CodeValidator;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\Block;

#[Service]
readonly class ChainVerifier
{
    public function verify(Block $block): void
    {
        $previous = null;

        foreach ($block as $statement) {
            foreach ($statement as $token) {
                if ($previous) {
                    if ($token->previous !== $previous) {
                        throw new PreviousTokenIsIncorrect($token, $previous);
                    }

                    if ($previous->next !== $token) {
                        throw new NextTokenIsIncorrect($token, $previous);
                    }
                }

                $previous = $token;
            }
        }
    }
}
