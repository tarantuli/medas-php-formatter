<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\TokenFormatters;

use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\TokenGroups;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12Whitespace implements TokenFormatter
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

    public function format(TokenCollection $tokens): void
    {
        $this->addSpaces($tokens);
        $this->removeSpaces($tokens);
    }

    private function addSpaces(TokenCollection $tokens): void
    {
        $spaceBeforeRequired = $this->getSpaceBeforeRequired();
        $spaceAfterRequired = $this->getSpaceAfterRequired();

        foreach ($tokens as $token) {
            // No spaces in a declare statement
            if ($token->statement->type instanceof DeclareStatement) {
                continue;
            }

            // One space between ") {"
            if ($token->is(T_CURLY_BRACKET_OPEN) && $token->previous->is(T_ROUND_BRACKET_CLOSE)) {
                $token->previous->spaceAfter = true;
            }

            if ($token->is($spaceAfterRequired)) {
                $token->spaceAfter = true;
            }

            if ($token->previous) {
                if ($token->is($spaceBeforeRequired)) {
                    $token->previous->spaceAfter = true;
                }
            }
        }
    }

    private function getSpaceBeforeRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            [
                T_VARIABLE,
                T_AMPERSAND,
                T_QUESTION_MARK,
                T_ELLIPSIS,
            ]
        );
    }

    private function getSpaceAroundRequired(): array
    {
        return array_merge(
            $this->tokenGroups->arithmicOperators(),
            $this->tokenGroups->assignmentOperators(),
            $this->tokenGroups->bitwiseOperators(),
            $this->tokenGroups->comparisonOperators(),
            $this->tokenGroups->controlKeywords(),
            $this->tokenGroups->logicalOperators(),
            $this->tokenGroups->typeOperators(),
            [
                T_ASSIGNMENT,
                T_DOUBLE_ARROW,
            ]
        );
    }

    private function getSpaceAfterRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            [
                T_COMMA,
                T_COLON,
            ]
        );
    }

    private function removeSpaces(TokenCollection $tokens): void
    {
        $spaceBeforeForbidden = $this->getSpaceBeforeForbidden();
        $spaceAfterForbidden = $this->getSpaceAfterForbidden();

        foreach ($tokens as $token) {
            if ($token->is($spaceAfterForbidden)) {
                $token->spaceAfter = false;
            }

            if ($token->previous) {
                if ($token->is($spaceBeforeForbidden)) {
                    $token->previous->spaceAfter = false;
                }

                // No space between inc/dec operators and variables
                if ($token->is(T_VARIABLE) && $token->previous->is([T_INC, T_DEC])) {
                    $token->previous->spaceAfter = false;
                }
            }
        }
    }

    private function getSpaceBeforeForbidden(): array
    {
        return [
            T_ROUND_BRACKET_CLOSE,
        ];
    }

    private function getSpaceAfterForbidden(): array
    {
        return [
            T_ROUND_BRACKET_OPEN,
            T_AMPERSAND,
            T_ELLIPSIS,
        ];
    }
}
