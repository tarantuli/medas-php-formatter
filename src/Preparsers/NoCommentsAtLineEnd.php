<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\MoveCommentsAtLineEnd;
use Medas\PhpFormatter\Tokens\TokenCollection;

#[Service]
class NoCommentsAtLineEnd implements Preparser
{
    private array $tokensToMove = [];

    public function __construct()
    {
    }

    public function preparse(TokenCollection $tokens): void
    {
        foreach ($tokens as $token) {
            if (!$token->is(T_COMMENT) || str_contains($token->previous->text, "\n")) {
                continue;
            }

            // This is a comment token that's at the end of a line, remember it.
            // MoveCommentsAtLineEnd will move it after the tree structure is determined
            $this->tokensToMove[] = $token;
        }
    }

    public function additionalFormatters(): array
    {
        // You can't inject this class in the constructor, as MoveCommentsAtLineEnd already injects *this* class
        return [service(MoveCommentsAtLineEnd::class)];
    }

    public function tokensToMove(): array
    {
        return $this->tokensToMove;
    }
}
