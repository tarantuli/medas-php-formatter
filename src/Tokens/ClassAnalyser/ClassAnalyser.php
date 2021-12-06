<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\BlockDumper;
use Medas\PhpBeautifier\Tokens\Contexts\MethodParameters;
use Medas\PhpBeautifier\Tokens\Contexts\MethodReturnType;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseTraitStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\PhpBeautifier\Tokens\Tokenizer;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ClassAnalyser
{
    private const CLASS_TYPES = [T_CLASS, T_INTERFACE, T_TRAIT];

    private const INTERNAL_TYPES = [
        'bool', 'int', 'float', 'string', 'array', 'object', 'callable', 'iterable',
        'resource', 'null', 'void', 'never', 'self', 'parent', 'static', 'mixed',
        'false',
    ];

    private const NS = '\\';
    private const REFERENCE_TYPES = [T_STRING, T_NAME_QUALIFIED, T_NAME_RELATIVE, T_NAME_FULLY_QUALIFIED];

    public function __construct(private Tokenizer $tokenizer)
    {
    }

    /** @noinspection RedundantSuppression */
    public function analyse(string $code): ClassAnalysis
    {
        $tokens = $this->tokenizer->tokenize($code);

        return $this->analyseTokenCollection($tokens);
    }

    public function analyseTokenCollection(TokenCollection $tokens): ClassAnalysis
    {
        $results = new ClassAnalysis();

        if (false) {
            /** @noinspection PhpUnreachableStatementInspection */
            \service(BlockDumper::class)->dump($tokens->structure);
        }

        $this->determineImports($tokens, $results);
        $this->determineNamespaceAndName($tokens, $results);
        $this->findReferences($tokens, $results);

        return $results;
    }

    private function determineImports(TokenCollection $tokens, ClassAnalysis $results)
    {
        foreach ($tokens as $token) {
            if ($token->is(T_USE) && $token->statement->type instanceof UseClassStatement) {
                $path = $token->next->text;

                if ($token->next->next->is(T_AS)) {
                    $reference = $token->next->next->next->text;
                }
                else {
                    $reference = $this->getLastPart($path);
                }

                $results->imports[] = new ClassReference($reference, self::NS . $path);
            }
        }
    }

    private function getLastPart(string $path): string
    {
        return str_contains($path, self::NS) ? substr($path, strrpos($path, self::NS) + 1) : $path;
    }

    private function determineNamespaceAndName(TokenCollection $tokens, ClassAnalysis $results)
    {
        foreach ($tokens as $token) {
            if ($token->is(T_NAMESPACE)) {
                $results->namespace = $token->next->text;
            }

            if ($token->is(self::CLASS_TYPES)) {
                $results->name = $token->next->text;
                $results->fqn = self::NS . $results->namespace . self::NS . $results->name;

                match ($token->id) {
                    T_CLASS => $results->isClass = true,
                    T_INTERFACE => $results->isInterface = true,
                    T_TRAIT => $results->isTrait = true,
                };

                if ($token->statement->containsType(T_FINAL)) {
                    $results->isFinal = true;
                }

                if ($token->statement->containsType(T_ABSTRACT)) {
                    $results->isAbstract = true;
                }
            }
        }
    }

    private function findReferences(TokenCollection $tokens, ClassAnalysis $results)
    {
        foreach ($tokens as $token) {
            if ($token->is(T_EXTENDS)) {
                // Class extension declaration, object instantiaion or instanceof comparison
                $results->extends = $this->resolveReference($results, $token->next);
            }

            if ($token->is(T_IMPLEMENTS)) {
                // Class implementation declaration, could be multiple
                $results->implements = $this->collectReferences($results, $token->next, T_COMMA);
            }

            if ($token->is([T_NEW, T_INSTANCEOF])) {
                // Object instantiaion or instanceof comparison
                $results->uses[] = $this->resolveReference($results, $token->next);
            }

            if ($token->is(T_USE) && $token->statement->type instanceof UseTraitStatement) {
                // A use trait statement
                $results->addUses($this->collectReferences($results, $token->next, T_COMMA));
            }

            if ($this->couldBeClassName($token)) {
                if ($token->next->is(T_DOUBLE_COLON)) {
                    // "Class::..."
                    $results->uses[] = $this->resolveReference($results, $token);
                }

                if ($token->context instanceof MethodParameters
                    || $token->context instanceof MethodReturnType) {
                    $results->uses[] = $this->resolveReference($results, $token);
                }
            }
        }
    }

    private function resolveReference(ClassAnalysis $results, Token $token): ClassReference
    {
        $reference = $token->text;
        $firstPart = $this->getFirstPart($reference);

        if ($firstPart === '') {
            // It's an absolute path
            return new ClassReference($reference, $reference);
        }

        $resolvedFirstPart = $results->resolveImport($firstPart);

        if ($resolvedFirstPart === null) {
            // It's a path relative to the namespace
            return new ClassReference($reference, self::NS . $results->namespace . self::NS . $reference);
        }
        else {
            // It's a path relative to an alias
            return new ClassReference(
                $reference,
                $resolvedFirstPart . substr($reference, strlen($firstPart))
            );
        }
    }

    private function getFirstPart(string $path): string
    {
        return str_contains($path, self::NS) ? substr($path, 0, strpos($path, self::NS)) : $path;
    }

    private function collectReferences(ClassAnalysis $results, Token $token, int|string $separator): array
    {
        $references = [];

        do {
            $references[] = $this->resolveReference($results, $token);
            $token = $token->next->next;
        } while ($token->previous->is($separator));

        return $references;
    }

    private function couldBeClassName(Token $token): bool
    {
        return $token->is(self::REFERENCE_TYPES) && !in_array($token->text, self::INTERNAL_TYPES, true);
    }
}
