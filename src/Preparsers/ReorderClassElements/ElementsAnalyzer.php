<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ElementsAnalyzer
{
    public const array TOKENS_TO_SKIP = [T_WHITESPACE, T_OPEN_TAG, T_COMMENT, T_DOC_COMMENT];

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
        $inDefinition = true;
        $inAttribute = false;
        $attributeDepth = 0;

        foreach ($tokens as $index => $token) {
            if (!is_array($token)) {
                switch ($token) {
                    case T_SQUARE_BRACKET_OPEN:
                        if ($inAttribute) {
                            ++$attributeDepth;
                        }

                        break;

                    case T_SQUARE_BRACKET_CLOSE:
                        if ($inAttribute) {
                            if ($attributeDepth === 0) {
                                $inAttribute = false;
                            }
                            else {
                                --$attributeDepth;
                            }
                        }

                        break;
                }

                continue;
            }

            [$type, $content] = $token;

            if (in_array($type, self::TOKENS_TO_SKIP)) {
                continue;
            }

            switch ($type) {
                case T_ATTRIBUTE:
                    if ($inDefinition) {
                        $inAttribute = true;
                    }

                    break;

                case T_USE:
                    if ($inDefinition) {
                        $element->isUse = Properties::IS_USE_STATEMENT;
                    }

                    break;

                case T_ABSTRACT:
                    if ($inDefinition) {
                        $element->isAbstract = Properties::IS_ABSTRACT;
                    }

                    break;

                case T_CONST:
                    if ($inDefinition) {
                        $element->isConst = Properties::IS_CONST;
                    }

                    break;

                case T_STATIC:
                    if ($inDefinition) {
                        $element->isStatic = Properties::IS_STATIC;
                    }

                    break;

                case T_PUBLIC:
                    if ($inDefinition) {
                        $element->accessModifier = Properties::IS_PUBLIC;
                    }

                    break;

                case T_PROTECTED:
                    if ($inDefinition) {
                        $element->accessModifier = Properties::IS_PROTECTED;
                    }

                    break;

                case T_PRIVATE:
                    if ($inDefinition) {
                        $element->accessModifier = Properties::IS_PRIVATE;
                    }

                    break;

                case T_FUNCTION:
                    if ($inDefinition) {
                        $element->isMethod = Properties::IS_METHOD;
                    }

                    break;

                case T_STRING:
                    if (!$inAttribute) {
                        if ($inDefinition) {
                            $element->name = $content;
                        }

                        $inDefinition = false;
                    }

                    break;

                default:
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

    private function determineKey(Element $element): void
    {
        $element->sortingKey = implode('', [
            $element->isUse,
            $element->isAbstract,
            $element->isConst,
            $element->isStatic,
            $element->isMethod,
            $element->isMagicMethod,
            $element->isMethod === Properties::IS_METHOD ? $element->accessModifier : 1,
        ]);
    }
}
