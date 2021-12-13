<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens\ClassAnalyser;

use Medas\PhpBeautifier\Tokens\Contexts\MethodParameters;
use Medas\PhpBeautifier\Tokens\Contexts\MethodReturnType;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseTraitStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ReferenceFinder
{
    private const INTERNAL_TYPES = [
        'bool', 'int', 'float', 'string', 'array', 'object', 'callable', 'iterable',
        'resource', 'null', 'void', 'never', 'self', 'parent', 'static', 'mixed',
        'false',
    ];

    private const REFERENCE_TYPES = [T_STRING, T_NAME_QUALIFIED, T_NAME_RELATIVE, T_NAME_FULLY_QUALIFIED];

    public function find(TokenCollection $tokens, ClassAnalysis $results)
    {
        foreach ($tokens as $token) {
            if ($token->is(T_EXTENDS)) {
                // Class extension declaration, object instantiaion or instanceof comparison
                $results->extends = $this->resolveReference($results, $token->next);
            }

            if ($token->is(T_IMPLEMENTS)) {
                // Class implementation declaration, could be multiple
                foreach ($this->collectTokens($token->next) as $implementToken) {
                    $this->addImplement($results, $implementToken);
                }
            }

            if ($token->is([T_NEW, T_INSTANCEOF])) {
                // Object instantiaion or instanceof comparison
                $this->addUsage($results, $token->next);
            }

            if ($token->is(T_USE) && $token->statement->type instanceof UseTraitStatement) {
                // A use trait statement
                foreach ($this->collectTokens($token->next) as $useToken) {
                    $this->addUsage($results, $useToken);
                }
            }

            if ($this->couldBeClassName($token)) {
                if ($token->next->is(T_DOUBLE_COLON)) {
                    // "Class::..."
                    $this->addUsage($results, $token);
                }

                if ($token->context instanceof MethodParameters
                    || $token->context instanceof MethodReturnType) {
                    $this->addUsage($results, $token);
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
            return new ClassReference($reference, '\\' . $results->namespace . '\\' . $reference);
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
        return str_contains($path, '\\') ? substr($path, 0, strpos($path, '\\')) : $path;
    }

    private function collectTokens(Token $token): array
    {
        $tokens = [];

        do {
            $tokens[] = $token;
            $token = $token->next->next;
        } while ($token->previous->is(T_COMMA));

        return $tokens;

    }

    private function addImplement(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->resolveReference($results, $token);
        $results->implements[$reference->reference] = $reference;
    }

    private function addUsage(ClassAnalysis $results, Token $token): void
    {
        $reference = $this->resolveReference($results, $token);
        $results->uses[$reference->reference] = $reference;
    }

    private function couldBeClassName(Token $token): bool

    {
        return $token->is(self::REFERENCE_TYPES) && !in_array($token->text, self::INTERNAL_TYPES, true);
    }
}
