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
        $name = '';

        foreach ($tokens as $token) {
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
                    $element->isFunction = Properties::IS_FUNCTION;

                    break;

                case T_VARIABLE:
                case T_STRING:
                    $name = $content;

                    break;

                default:
                    // Everything else is irrelevant
                    break;
            }
        }

        if (str_starts_with($name, '__')) {
            if ($name === '__construct') {
                $element->isMagicMethod = Properties::IS_CONSTRUCTOR;
            }
            elseif ($name === '__destruct') {
                $element->isMagicMethod = Properties::IS_DESTRUCTOR;
            }
            elseif ($name === '__toString') {
                $element->isMagicMethod = Properties::IS__TO_STRING;
            }
            elseif ($name === '__get') {
                $element->isMagicMethod = Properties::IS___GET;
            }
            elseif ($name === '__set') {
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
            $element->isFunction,
            $element->isMagicMethod,
            $element->accessModifier,
        ]);
    }
}
