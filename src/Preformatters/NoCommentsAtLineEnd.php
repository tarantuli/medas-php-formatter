<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preformatters;

use Medas\Core\{Attributes\Service, Interfaces\ServiceManager};
use Medas\PhpFormatter\Formatters\Replacements\MoveCommentsAtLineEnd;
use Medas\PhpFormatter\Job;

#[Service]
class NoCommentsAtLineEnd implements Preformatter
{
    private array $tokensToMove = [];

    public function __construct(
        private readonly ServiceManager $serviceManager,
    )
    {
    }

    public function preformat(Job $job): void
    {
        $this->tokensToMove = [];

        foreach ($job->tokens as $token) {
            if (!$token->is(T_COMMENT)
                    || str_contains($token->previous->text, "\n")
                    || $token->previous->is([T_OPEN_TAG, T_CURLY_BRACKET_OPEN])) {
                continue;
            }

            // This is a comment token at the end of a line, remember it.
            // MoveCommentsAtLineEnd will move it after the tree structure is determined
            $this->tokensToMove[] = $token;
        }
    }

    public function additionalFormatters(): array
    {
        // You can't inject this class in the constructor, as MoveCommentsAtLineEnd already injects *this* class
        return [$this->serviceManager->resolve(MoveCommentsAtLineEnd::class)];
    }

    public function tokensToMove(): array
    {
        return $this->tokensToMove;
    }
}
