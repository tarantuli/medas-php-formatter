<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Tokens\Contexts\GlobalScope;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ContextFinder
{
    public function __construct(private StatementTypeFinder $typeFinder)
    {
    }

    public function determine(Block $block): void
    {
        $context = new GlobalScope();
        $globalScopeDepth = null;
        $nextStatementIsClassBody = false;
        $nextStatementIsMethodBody = false;

        foreach ($block as $statement) {
            if ($statement->block->depth === $globalScopeDepth) {
                $context = new GlobalScope();
                $globalScopeDepth = null;
            }

            if ($nextStatementIsClassBody) {
                $context = new Contexts\ClassBody();
                $nextStatementIsClassBody = false;
            }

            if ($nextStatementIsMethodBody) {
                $context = new Contexts\MethodBody();
                $nextStatementIsMethodBody = false;
            }

            $statementType = $this->typeFinder->for($statement);

            if ($statementType instanceof ClassDeclaration) {
                $context = new Contexts\ClassDeclaration();
                $globalScopeDepth = $statement->block->depth;
                $nextStatementIsClassBody = true;
            }

            if ($statementType instanceof FunctionDeclaration) {
                $context = new Contexts\MethodDeclaration();
                $nextStatementIsMethodBody = true;
                $openParentheses = 0;
            }

            foreach ($statement as $token) {
                $token->context = $context;

                if ($statementType instanceof FunctionDeclaration) {
                    // The context of the following tokens may change
                    if ($token->is(T_ROUND_BRACKET_OPEN)) {
                        ++$openParentheses;
                        $context = new Contexts\MethodParameters();
                    }
                    if ($token->is(T_ROUND_BRACKET_CLOSE)) {
                        $token->context = new Contexts\MethodDeclaration();
                        if (--$openParentheses === 0) {
                            $context = new Contexts\MethodReturnType();
                        }
                    }
                }
            }
        }
    }
}
