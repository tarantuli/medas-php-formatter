<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Tokens, Job};
use Medas\PhpTokenizer\{
    BlockType,
    StatementTypeFinder,
    StatementTypes\DeclareStatement,
    StatementTypes\FunctionDeclaration,
    StatementTypes\UseTraitStatement,
    Token,
    TokenGroups,
    TokenRole,
    TokenTree,
    TokenType,
    TypeDeclarationKind
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
    private array $keywords;

    public function __construct(
        private StatementTypeFinder $typeFinder,
        private TokenGroups         $tokenGroups,
    )
    {
        $this->spaceBeforeRequired = $this->getSpaceBeforeRequired();
        $this->spaceAfterRequired = $this->getSpaceAfterRequired();
        $this->spaceBeforeForbidden = $this->getSpaceBeforeForbidden();
        $this->spaceAfterForbidden = $this->getSpaceAfterForbidden();
        $this->keywords = $this->tokenGroups->keywords();

        $this->operatorsAndKeywords = array_merge(
            $this->tokenGroups->keywords(),
            $this->tokenGroups->symbolOperators()
        );

        $this->operatorsKeywordsAndBrackets = array_merge(
            $this->operatorsAndKeywords,
            $this->tokenGroups->brackets()
        );
    }

    private function getSpaceBeforeRequired(): array
    {
        return array_merge(
            $this->getSpaceAroundRequired(),
            [
                ...TokenType::AMPERSANDS,
                T_ELLIPSIS,
                TokenType::VERTICAL_BAR,
                TokenType::QUESTION_MARK,
                TokenType::SQUARE_BRACKET_OPEN,
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
                TokenType::COMMA,
                T_NEW,
                TokenType::SEMICOLON,
                T_THROW,
            ],
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
                ...TokenType::AMPERSANDS,
                T_AS,
                TokenType::ASSIGNMENT,
                T_CASE,
                TokenType::COLON,
                T_DOC_COMMENT,
                T_DOUBLE_ARROW,
                T_FINALLY,
                TokenType::VERTICAL_BAR,
                TokenType::QUESTION_MARK,
                T_RETURN,
            ],
        );
    }

    private function getSpaceBeforeForbidden(): array
    {
        return [
            T_INLINE_HTML,
            TokenType::ROUND_BRACKET_CLOSE,
            TokenType::SEMICOLON,
            TokenType::SQUARE_BRACKET_CLOSE,
        ];
    }

    private function getSpaceAfterForbidden(): array
    {
        return [
            TokenType::CURLY_BRACKET_OPEN,
            T_CURLY_OPEN,
            T_DOLLAR_OPEN_CURLY_BRACES,
            T_DOUBLE_COLON,
            T_ELLIPSIS,
            T_ENCAPSED_AND_WHITESPACE,
            TokenType::EXCLAMATION_POINT,
            T_INLINE_HTML,
            T_OBJECT_OPERATOR,
            TokenType::ROUND_BRACKET_OPEN,
            TokenType::SQUARE_BRACKET_OPEN,
        ];
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
        $squareDepth = 0;

        foreach ($tree as $token) {
            // No spaces in a declare statement
            $statementType = $this->typeFinder->for($token->statement);

            if ($statementType instanceof DeclareStatement) {
                continue;
            }

            if ($token->is(T_ATTRIBUTE) || ($token->inAttribute && $token->is(TokenType::SQUARE_BRACKET_OPEN))) {
                ++$squareDepth;
            }

            if ($token->is($this->spaceAfterRequired)) {
                $token->spaceAfter();
            }

            // One space between ") {", "): <type> {", "namespace X {", "use A, B {" and "new class {". Not in a class
            // declaration, which has a line break before its {.
            $spaceBefore = in_array($token->opensBlock, [BlockType::Namespace, BlockType::TraitAdaptation], true)
                || ($token->opensBlock === BlockType::ClassLike && $token->statement->declaration === null);

            if ($token->is(TokenType::CURLY_BRACKET_OPEN)
                    && ($this->followsParenthesesOrReturnType($token) || $spaceBefore)) {
                $token->previous->spaceAfter();
            }

            // No space between "?type"
            elseif ($token->role === TokenRole::NullableMarker) {
                $token->spaceAfter(false);
            }

            // A space after attributes
            elseif ($token->inAttribute && $token->is(TokenType::SQUARE_BRACKET_CLOSE)) {
                --$squareDepth;

                if ($squareDepth === 0) {
                    $token->spaceAfter();
                }
            }

            if ($token->previous) {
                if ($token->is($this->spaceBeforeRequired) && !$token->inString) {
                    $token->previous->spaceAfter();
                }

                // A space between a closing bracket and a keyword, like in "new class(1) extends A", which PHP
                // doesn't need
                if ($token->is($this->keywords)
                        && $token->previous->is([TokenType::ROUND_BRACKET_CLOSE, TokenType::SQUARE_BRACKET_CLOSE])) {
                    $token->previous->spaceAfter();
                }

                // No space after the & of a reference, like in "function f(int &...$a)" or "$a = &$b"
                if ($token->previous->role === TokenRole::ByReference && $token->is(Tokens::VARIABLE_STARTERS)) {
                    $token->previous->spaceAfter(false);
                }

                // No space before a colon, like the one of a return type, a named argument, "case 1:" or "?:",
                // except before the "else" colon of a ternary
                elseif ($token->is(TokenType::COLON) && $token->role !== TokenRole::TernaryElse) {
                    $token->previous->spaceAfter(false);
                }

                // No space around the "|" of a union type
                elseif ($token->is(TokenType::VERTICAL_BAR) && $token->typeDeclaration !== null) {
                    $token->previous->spaceAfter(false);
                    $token->spaceAfter(false);
                }

                // No space around the "&" of an intersection type, as in (A&B)|null
                elseif ($token->is(TokenType::AMPERSANDS) && $token->typeDeclaration !== null) {
                    $token->previous->spaceAfter(false);
                    $token->spaceAfter(false);
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

    private function followsParenthesesOrReturnType(Token $brace): bool
    {
        return $brace->previous?->is(TokenType::ROUND_BRACKET_CLOSE)
            || $brace->previous?->typeDeclaration === TypeDeclarationKind::ReturnType;
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
                        && $token->is([TokenType::PLUS, TokenType::MINUS])
                        && $token->next->is(TokenType::ROUND_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No space between pluses, minuses and ampersands before numbers and variables
                // that follow an operator or bracket
                elseif ($token->is([TokenType::PLUS, TokenType::MINUS, ...TokenType::AMPERSANDS])
                        && $token->next->is([T_LNUMBER, T_DNUMBER, T_VARIABLE, T_STRING])) {
                    if ($token->previous->is([TokenType::ROUND_BRACKET_CLOSE, TokenType::SQUARE_BRACKET_CLOSE])) {
                        $token->spaceAfter();
                    }
                    elseif ($token->previous->is($this->operatorsKeywordsAndBrackets)) {
                        $token->spaceAfter(false);
                    }
                }

                // No spaces between variables and [
                elseif ($token->is([T_VARIABLE, TokenType::ROUND_BRACKET_CLOSE, T_STRING])
                        && $token->next->is(TokenType::SQUARE_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No spaces between ] and [
                elseif ($token->is(TokenType::SQUARE_BRACKET_CLOSE)
                        && $token->next->is(TokenType::SQUARE_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }

                // No space between "static" and "()"
                elseif ($token->is(T_STATIC) && $token->next->is(TokenType::ROUND_BRACKET_OPEN)) {
                    $token->spaceAfter(false);
                }
            }
        }
    }
}
