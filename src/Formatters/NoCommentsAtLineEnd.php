<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Formatters\Phases\BeforeStrippingWhitespace;
use Medas\PhpBeautifier\Formatters\Phases\FormattingPhase;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class NoCommentsAtLineEnd extends BaseFormatter
{
    public array $tokensToMove = [];

    public function format(TokenCollection $tokens): void
    {
        foreach ($tokens as $token) {
            if (!$token->is(T_COMMENT) || str_contains($token->previous->text, "\n")) {
                continue;
            }

            // This is a comment token that's at the end of a line, remember it
            $this->tokensToMove[] = $token;
        }
    }

    public function applyWhen(): FormattingPhase
    {
        return new BeforeStrippingWhitespace();
    }
}
