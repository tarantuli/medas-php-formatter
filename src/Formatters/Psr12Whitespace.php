<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters;

use Medas\PhpBeautifier\Tokens\Contexts\MethodReturnType;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\SwitchBranch;
use Medas\PhpBeautifier\Tokens\TokenGroups;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12Whitespace extends BaseFormatter
{
    public function __construct(private TokenGroups         $tokenGroups,
                                private StatementTypeFinder $typeFinder)
    {
    }

    public function format(TokenTree $tree): void
    {
        $this->addSpaces($tree);
        $this->removeSpaces($tree);
    }

    private function addSpaces(TokenTree $tree): void
    {
        $spaceBeforeRequired = $this->getSpaceBeforeRequired();
        $spaceAfterRequired = $this->getSpaceAfterRequired();

        foreach ($tree as $token) {
            // No spaces in a declare statement
            $statementType = $this->typeFinder->for($token->statement);

            if ($statementType instanceof DeclareStatement) {
                continue;
            }

            // One space between ") {"
            if ($token->is(T_CURLY_BRACKET_OPEN) && $token->previous->is(T_ROUND_BRACKET_CLOSE)) {
                $token->previous->spaceAfter = true;
            }

            if ($token->is($spaceAfterRequired)) {
                $token->spaceAfter = true;
            }

            // No space between "?type"
            if ($token->is(T_QUESTION_MARK) &&
                ($statementType instanceof ClassPropertyDeclaration || $statementType instanceof FunctionDeclaration)) {
                $token->spaceAfter = false;
            }

            if ($token->previous) {
                if ($token->is($spaceBeforeRequired)) {
                    $token->previous->spaceAfter = true;
                }

                // No space between "):" in return type declarations
                if ($token->is(T_COLON) && $token->context instanceof MethodReturnType) {
                    $token->previous->spaceAfter = false;
                }

                // No space between "?:"
                if ($token->is(T_COLON) && $token->previous->is(T_QUESTION_MARK)) {
                    $token->previous->spaceAfter = false;
                }

                // No space before ":" in case/default statements if it's the last token
                if ($token->is(T_COLON)
                    && $statementType instanceof SwitchBranch
                    && $token->isLastToken()) {
                    $token->previous->spaceAfter = false;
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
                T_SQUARE_BRACKET_OPEN,
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
                T_COLON,
                T_QUESTION_MARK,
                T_CASE,
                T_DOC_COMMENT,
            ]
        );
    }

    private function getSpaceAfterRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            [
                T_COMMA,
                T_SEMICOLON,
            ]
        );
    }

    private function removeSpaces(TokenTree $tree): void
    {
        $spaceBeforeForbidden = $this->getSpaceBeforeForbidden();
        $spaceAfterForbidden = $this->getSpaceAfterForbidden();
        $symbolOperators = $this->tokenGroups->symbolOperators();

        foreach ($tree as $token) {
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

                // No space between pluses and minuses before numbers, that follow an operator
                if ($token->next) {
                    if ($token->previous->is($symbolOperators)
                        && $token->is([T_PLUS, T_MINUS])
                        && $token->next->is([T_LNUMBER, T_DNUMBER])
                    ) {
                        $token->spaceAfter = false;
                    }
                }
            }
        }
    }

    private function getSpaceBeforeForbidden(): array
    {
        return [
            T_ROUND_BRACKET_CLOSE,
            T_SQUARE_BRACKET_CLOSE,
        ];
    }

    private function getSpaceAfterForbidden(): array
    {
        return [
            T_ROUND_BRACKET_OPEN,
            T_SQUARE_BRACKET_OPEN,
            T_AMPERSAND,
            T_ELLIPSIS,
            T_PIPE,
        ];
    }
}
