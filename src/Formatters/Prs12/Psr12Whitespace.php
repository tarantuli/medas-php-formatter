<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Prs12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\ReturnTypeTokens, Tokens};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    Contexts\MethodParameters,
    Contexts\MethodReturnType,
    StatementTypeFinder,
    StatementTypes\DeclareStatement,
    StatementTypes\FunctionDeclaration,
    StatementTypes\SwitchBranch,
    StatementTypes\UseTraitStatement,
    TokenGroups,
    TokenTree
};

#[Service]
readonly class Psr12Whitespace extends BaseFormatter
{
    private array $spaceBeforeRequired;
    private array $spaceAfterRequired;
    private array $spaceBeforeForbidden;
    private array $spaceAfterForbidden;
    private array $operatorsAndKeywords;
    private array $operatorsKeywordsAndBrackets;

    public function __construct(
        private TokenGroups         $tokenGroups,
        private StatementTypeFinder $typeFinder,
        private ReturnTypeTokens    $returnTypeTokens,
    )
    {
        $this->spaceBeforeRequired = $this->getSpaceBeforeRequired();
        $this->spaceAfterRequired = $this->getSpaceAfterRequired();
        $this->spaceBeforeForbidden = $this->getSpaceBeforeForbidden();
        $this->spaceAfterForbidden = $this->getSpaceAfterForbidden();

        $this->operatorsAndKeywords = array_merge(
            $this->tokenGroups->keywords(),
            $this->tokenGroups->symbolOperators()
        );

        $this->operatorsKeywordsAndBrackets = array_merge(
            $this->operatorsAndKeywords,
            $this->tokenGroups->brackets()
        );
    }

    public function priority(): int
    {
        return 1200;
    }

    public function format(Job $job): void
    {
        $this->addSpaces($job->tree);
        $this->removeSpaces($job->tree);
    }

    private function addSpaces(TokenTree $tree): void
    {
        $openTernaries = 0;

        foreach ($tree as $token) {
            // No spaces in a declare statement
            $statementType = $this->typeFinder->for($token->statement);

            if ($statementType instanceof DeclareStatement) {
                continue;
            }

            if ($token->is($this->spaceAfterRequired)) {
                $token->spaceAfter();
            }

            // One space between ") {" and "): <type> {"
            if ($token->is(T_CURLY_BRACKET_OPEN) && $this->returnTypeTokens->isReturnTypeToken($token)) {
                $token->previous->spaceAfter();
            }

            // No space between "?type"
            elseif ($token->is(T_QUESTION_MARK)) {
                if ($token->inTypeDeclaration) {
                    $token->spaceAfter(false);
                }
                else {
                    ++$openTernaries;
                }
            }

            if ($token->previous) {
                if ($token->is($this->spaceBeforeRequired) && !$token->inString) {
                    $token->previous->spaceAfter();
                }

                // No space between & and variable starters in method parameters
                if ($token->previous->is(T_AMPERSAND)
                        && $token->is(Tokens::VARIABLE_STARTERS)
                        && $token->context instanceof MethodParameters) {
                    $token->previous->spaceAfter(false);
                }
                elseif ($token->is(T_COLON)) {
                    // No space between "):" in return type declarations
                    if ($token->context instanceof MethodReturnType) {
                        $token->previous->spaceAfter(false);
                    }

                    // No space between "?:"
                    elseif ($token->previous->is(T_QUESTION_MARK)) {
                        $token->previous->spaceAfter(false);
                    }

                    // No space before ":" in case/default statements if it's the last token
                    elseif ($statementType instanceof SwitchBranch && $token->isLastToken()) {
                        $token->previous->spaceAfter(false);
                    }

                    // No space between strings and ":" in named arguments
                    if ($openTernaries === 0) {
                        $token->previous->spaceAfter(false);
                    }
                    else {
                        --$openTernaries;
                    }
                }

                // No space around "|" in function declarations and catch statements
                elseif ($token->is(T_PIPE)) {
                    $stripSpaces = $token->inTypeDeclaration
                        || $token->statement->getToken(1)->is(T_CATCH)
                        || $this->returnTypeTokens->isReturnTypeToken($token->previous);

                    if ($stripSpaces) {
                        $token->previous->spaceAfter(false);
                        $token->spaceAfter(false);
                    }
                }
                elseif ($token->previous->is(T_FUNCTION) && !$statementType instanceof FunctionDeclaration) {
                    $token->previous->spaceAfter();
                }
                elseif ($token->is(T_USE) && !$statementType instanceof UseTraitStatement) {
                    $token->previous->spaceAfter();
                    $token->spaceAfter();
                }
            }
        }
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
                T_AMPERSAND,
                T_AS,
                T_ASSIGNMENT,
                T_CASE,
                T_COLON,
                T_DOC_COMMENT,
                T_DOUBLE_ARROW,
                T_PIPE,
                T_QUESTION_MARK,
                T_RETURN,
            ],
        );
    }

    private function getSpaceBeforeRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            [
                T_AMPERSAND,
                T_ELLIPSIS,
                T_PIPE,
                T_QUESTION_MARK,
                T_SQUARE_BRACKET_OPEN,
                T_VARIABLE,
            ],
        );
    }

    private function getSpaceAfterRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            $this->tokenGroups->casts(),
            [
                T_COMMA,
                T_NEW,
                T_SEMICOLON,
                T_THROW,
            ],
        );
    }

    private function removeSpaces(TokenTree $tree): void
    {
        foreach ($tree as $token) {
            if ($token->is($this->spaceAfterForbidden)) {
                $token->spaceAfter(false);
            }

            if (!$token->previous) {
                continue;
            }

            if ($token->is($this->spaceBeforeForbidden)) {
                $token->previous->spaceAfter(false);
            }

            // No space between inc/dec operators and variables
            if ($token->is(T_VARIABLE) && $token->previous->is([T_INC, T_DEC])) {
                $token->previous->spaceAfter(false);
            }

            if ($token->next) {
                // No space between pluses and minuses followed by opening round brackets
                if ($token->previous->is($this->operatorsAndKeywords)
                        && $token->is([T_PLUS, T_MINUS])
                        && $token->next->is(T_ROUND_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No space between pluses, minuses and ampersands before numbers and variables,
                // that follow an operator or bracket
                elseif ($token->is([T_PLUS, T_MINUS, T_AMPERSAND])
                        && $token->next->is([T_LNUMBER, T_DNUMBER, T_VARIABLE, T_STRING])) {
                    if ($token->previous->is([T_ROUND_BRACKET_CLOSE, T_SQUARE_BRACKET_CLOSE])) {
                        $token->spaceAfter();
                    }
                    elseif ($token->previous->is($this->operatorsKeywordsAndBrackets)) {
                        $token->spaceAfter(false);
                    }
                }

                // No spaces between variables and [
                elseif ($token->is([T_VARIABLE, T_ROUND_BRACKET_CLOSE, T_STRING])
                        && $token->next->is(T_SQUARE_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No spaces between ] and [
                elseif ($token->is(T_SQUARE_BRACKET_CLOSE) && $token->next->is(T_SQUARE_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No space between "static" and "()"
                elseif ($token->is(T_STATIC) && $token->next->is(T_ROUND_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }
            }
        }
    }

    private function getSpaceBeforeForbidden(): array
    {
        return [
            T_ROUND_BRACKET_CLOSE,
            T_SEMICOLON,
            T_SQUARE_BRACKET_CLOSE,
        ];
    }

    private function getSpaceAfterForbidden(): array
    {
        return [
            T_CURLY_BRACKET_OPEN,
            T_DOUBLE_COLON,
            T_ELLIPSIS,
            T_ENCAPSED_AND_WHITESPACE,
            T_EXCLAMATION_POINT,
            T_OBJECT_OPERATOR,
            T_ROUND_BRACKET_OPEN,
            T_SQUARE_BRACKET_OPEN,
        ];
    }
}
