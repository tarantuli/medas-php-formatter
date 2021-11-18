<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class TokenGroups
{
    public function getTexts(): array
    {
        return array_merge(
            $this->getKeywords(),
            $this->getTypeOperators(),
            $this->getCasts(),
            $this->getLanguageConstructs()
        );
    }

    public function getKeywords(): array
    {
        return [
            T_ABSTRACT,
            T_AS,
            T_BREAK,
            T_CALLABLE,
            T_CASE,
            T_CATCH,
            T_CLASS,
            T_CLONE,
            T_CONST,
            T_CONTINUE,
            T_DEFAULT,
            T_DO,
            T_ECHO,
            T_ELSE,
            T_ELSEIF,
            T_ENDDECLARE,
            T_ENDFOR,
            T_ENDFOREACH,
            T_ENDIF,
            T_ENDSWITCH,
            T_ENDWHILE,
            T_EXIT,
            T_EXTENDS,
            T_FINAL,
            T_FINALLY,
            T_FOR,
            T_FOREACH,
            T_FUNCTION,
            T_GLOBAL,
            T_GOTO,
            T_IF,
            T_IMPLEMENTS,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_INSTEADOF,
            T_INTERFACE,
            T_MATCH,
            T_NAMESPACE,
            T_NEW,
            T_PRIVATE,
            T_PROTECTED,
            T_PUBLIC,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_RETURN,
            T_STATIC,
            T_SWITCH,
            T_THROW,
            T_TRAIT,
            T_TRY,
            T_USE,
            T_VAR,
            T_WHILE,
            T_YIELD,
            T_YIELD_FROM,
            T_LOGICAL_AND,
            T_LOGICAL_OR,
            T_LOGICAL_XOR,
        ];
    }

    public function getTypeOperators(): array
    {
        return [
            T_INSTANCEOF,
        ];
    }

    public function getCasts(): array
    {
        return [
            T_ARRAY_CAST,
            T_BOOL_CAST,
            T_DOUBLE_CAST,
            T_INT_CAST,
            T_OBJECT_CAST,
            T_STRING_CAST,
            T_UNSET_CAST,
        ];
    }

    public function getLanguageConstructs(): array
    {
        return [
            T_ARRAY,
            T_DECLARE,
            T_EMPTY,
            T_EVAL,
            T_FN,
            T_HALT_COMPILER,
            T_ISSET,
            T_LIST,
            T_PRINT,
            T_UNSET,
        ];
    }

    public function getSymbolOperators(): array
    {
        return array_merge(
            $this->getArithmicOperators(),
            $this->getAssignmentOperators(),
            $this->getBitwiseOperators(),
            $this->getBrackets(),
            $this->getComparisonOperators(),
            $this->getLogicalOperators(),
            $this->getTypeOperators(),
            [
                T_AMPERSAND,
                T_ASSIGNMENT,
                T_AT,
                T_COLON,
                T_DOLLAR,
                T_DOUBLE_ARROW,
                T_DOUBLE_QUOTE,
                T_ELLIPSIS,
                T_EXCLAMATION_POINT,
                T_NS_SEPARATOR,
                T_QUESTION_MARK,
                T_SEMICOLON,
            ]
        );
    }

    public function getArithmicOperators(): array
    {
        return [
            T_POW,
            T_MINUS,
            T_MOD,
            T_ASTERISK,
            T_CONCATENATOR,
            T_SLASH,
            T_PLUS,
        ];
    }

    public function getAssignmentOperators(): array
    {
        return [
            T_AND_EQUAL,
            T_COALESCE_EQUAL,
            T_CONCAT_EQUAL,
            T_DIV_EQUAL,
            T_MINUS_EQUAL,
            T_MOD_EQUAL,
            T_MUL_EQUAL,
            T_OR_EQUAL,
            T_PLUS_EQUAL,
            T_POW_EQUAL,
            T_SL_EQUAL,
            T_SR_EQUAL,
            T_XOR_EQUAL,
        ];
    }

    public function getBitwiseOperators(): array
    {
        return [
            T_SL,
            T_SR,
        ];
    }

    public function getBrackets(): array
    {
        return [
            T_ROUND_BRACKET_OPEN,
            T_ROUND_BRACKET_CLOSE,
            T_SQUARE_BRACKET_OPEN,
            T_SQUARE_BRACKET_CLOSE,
            T_CURLY_BRACKET_OPEN,
            T_CURLY_BRACKET_CLOSE,
        ];
    }

    public function getComparisonOperators(): array
    {
        return [
            T_COALESCE,
            T_IS_EQUAL,
            T_IS_GREATER_OR_EQUAL,
            T_IS_IDENTICAL,
            T_IS_NOT_EQUAL,
            T_IS_NOT_IDENTICAL,
            T_IS_SMALLER_OR_EQUAL,
            T_SPACESHIP,
            T_LESS_THAN,
            T_MORE_THAN,
        ];
    }

    public function getLogicalOperators(): array
    {
        return [
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
        ];
    }

    public function getIncDecOperators(): array
    {
        return [
            T_DEC,
            T_INC,
        ];
    }

    public function getLiterals(): array
    {
        return [
            T_BAD_CHARACTER,
            T_CONSTANT_ENCAPSED_STRING,
            T_ENCAPSED_AND_WHITESPACE,
            T_NAME_FULLY_QUALIFIED,
            T_NAME_RELATIVE,
            T_NAME_QUALIFIED,
            T_LNUMBER,
            T_DNUMBER,
            T_STRING,
        ];
    }

    public function getObjectOperators(): array
    {
        return [
            T_DOUBLE_COLON,
            T_NULLSAFE_OBJECT_OPERATOR,
            T_OBJECT_OPERATOR,
        ];
    }

    public function getMagicConstants(): array
    {
        return [
            T_CLASS_C,
            T_DIR,
            T_FILE,
            T_FUNC_C,
            T_LINE,
            T_METHOD_C,
            T_NS_C,
            T_TRAIT_C,
        ];
    }

    public function getHtmlTags(): array
    {
        return [
            T_CLOSE_TAG,
            T_OPEN_TAG,
            T_OPEN_TAG_WITH_ECHO,
        ];
    }

    public function getComments(): array
    {
        return [
            T_COMMENT,
            T_DOC_COMMENT,
        ];
    }

    public function getVariables(): array
    {
        return [
            T_CURLY_OPEN,
            T_DOLLAR_OPEN_CURLY_BRACES,
            T_NUM_STRING,
            T_VARIABLE,
        ];
    }

    public function getOtherTokens(): array
    {
        return [
            T_ATTRIBUTE,
            T_END_HEREDOC,
            T_INLINE_HTML,
            T_START_HEREDOC,
            T_WHITESPACE,
        ];
    }
}
