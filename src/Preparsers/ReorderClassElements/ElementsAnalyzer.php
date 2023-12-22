<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ElementsAnalyzer
{
    public const TOKENS_TO_SKIP = [T_WHITESPACE, T_OPEN_TAG, T_COMMENT, T_DOC_COMMENT];

    public function analyze(ReorderingJob $job): void
    {
        foreach ($job->elements as $element) {
            $this->analyzeElement($element);
            $this->determineKey($element);
        }
    }

    private function analyzeElement(Element $element): void
    {
        $tokens = token_get_all('<?php ' . $element->text);

        foreach ($tokens as $index => $token) {
            if (!is_array($token)) {
                continue;
            }

            [$type, $content] = $token;

            if (in_array($type, self::TOKENS_TO_SKIP)) {
                continue;
            }

            switch ($type) {
                case T_USE:
                    $element->isUse = Properties::IS_USE_STATEMENT;

                    break;

                case T_ABSTRACT:
                    $element->isAbstract = Properties::IS_ABSTRACT;

                    break;

                case T_CONST:
                    $element->isConst = Properties::IS_CONST;

                    break;

                case T_STATIC:
                    $element->isStatic = Properties::IS_STATIC;

                    break;

                case T_PUBLIC:
                    $element->accessModifier = Properties::IS_PUBLIC;

                    break;

                case T_PROTECTED:
                    $element->accessModifier = Properties::IS_PROTECTED;

                    break;

                case T_PRIVATE:
                    $element->accessModifier = Properties::IS_PRIVATE;

                    break;

                case T_FUNCTION:
                    $element->isMethod = Properties::IS_METHOD;

                    break;

                case T_STRING:
                    if ($element->name === null) {
                        $element->name = $content;
                    }

                    break;

                default:
                    // Everything else is irrelevant
                    $this->checkForReference($element, $tokens, $index, $type, $content);

                    break;
            }
        }

        if ($element->name !== null && str_starts_with($element->name, '__')) {
            if ($element->name === '__construct') {
                $element->isMagicMethod = Properties::IS_CONSTRUCTOR;
            }
            elseif ($element->name === '__destruct') {
                $element->isMagicMethod = Properties::IS_DESTRUCTOR;
            }
            elseif ($element->name === '__toString') {
                $element->isMagicMethod = Properties::IS__TO_STRING;
            }
            elseif ($element->name === '__get') {
                $element->isMagicMethod = Properties::IS___GET;
            }
            elseif ($element->name === '__set') {
                $element->isMagicMethod = Properties::IS___SET;
            }
            else {
                $element->isMagicMethod = Properties::IS_OTHER_MAGIC_METHOD;
            }
        }
    }

    private function determineKey(Element $element): void
    {
        $element->sortingKey = implode('', [
            $element->isUse,
            $element->isAbstract,
            $element->isConst,
            $element->isStatic,
            $element->isMethod,
            $element->isMagicMethod,
            $element->accessModifier,
        ]);
    }

    private function checkForReference(
        Element    $element,
        array      $tokens,
        int        $index,
        int|string $type,
        string     $content
    ): void
    {
        if ($type !== T_VARIABLE || $content !== '$this') {
            return;
        }

        if (!isset($tokens[$index + 3])) {
            return;
        }

        if ($tokens[$index + 1][0] !== T_OBJECT_OPERATOR) {
            return;
        }

        if ($tokens[$index + 2][0] !== T_STRING) {
            return;
        }

        if ($tokens[$index + 3][0] !== T_ROUND_BRACKET_OPEN) {
            return;
        }

        $calledName = $tokens[$index + 2][1];
        $element->methodReferences[] = $calledName;
    }
}
