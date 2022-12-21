<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\PhpFormatter\Tokens\Contexts\ClassBody;
use Medas\PhpFormatter\Tokens\Contexts\GlobalScope;
use Medas\PhpFormatter\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\UseTraitStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class ContextAdder
{
    public function __construct(
        private readonly StatementTypeFinder $typeFinder,
    )
    {
    }

    public function add(TokenTree $tree): void
    {
        $context = new GlobalScope();
        $globalScopeDepth = null;
        $classBodyDepth = null;
        $nextStatementIsClassBody = false;
        $nextStatementIsMethodBody = false;

        foreach ($tree->statements() as $statement) {
            if ($statement->block->depth === $globalScopeDepth) {
                $context = new GlobalScope();
                $globalScopeDepth = null;
            }

            if ($statement->block->depth === $classBodyDepth) {
                $context = new ClassBody();
            }

            if ($nextStatementIsClassBody) {
                $context = new Contexts\ClassBody();
                $classBodyDepth = $statement->block->depth;
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

            // If the statement type is UseClassStatement, but we're already in a class body,
            // then it's a UseTraitStatement. The type finder can't know that, but we can.
            if ($statementType instanceof UseClassStatement && $context instanceof ClassBody) {
                $statement->setType(new UseTraitStatement());
            }

            if ($statementType instanceof FunctionDeclaration) {
                // In the context of a class body, this is a method declaration; otherwise, it's a function declaration
                $context = ($context instanceof Contexts\ClassBody)
                    ? new Contexts\MethodDeclaration()
                    : new Contexts\FunctionDeclaration();

                // If it ends in a semicolon, it's an abstract or interface declaration
                // If it ends in a curly bracket open, a body will follow
                if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                    $nextStatementIsMethodBody = true;
                }

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
