<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Tokens\StatementTypes\AttributeStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlankLine;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlockCloser;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\StatementType;
use Medas\PhpBeautifier\Tokens\StatementTypes\UnknownType;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseConstStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseFunctionStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class StatementTypeFinder
{
    public function for(Statement $statement): StatementType
    {
        if (isset($statement->type)) {
            return $statement->type;
        }

        return $statement->type = $this->determineType($statement);
    }

    private function determineType(Statement $statement): StatementType
    {
        $firstToken = $statement->firstToken();

        if (null === $firstToken) {
            return new BlankLine();
        }

        if ($firstToken->is(T_DECLARE)) {
            return new DeclareStatement();
        }

        if ($firstToken->is(T_NAMESPACE)) {
            return new NamespaceDeclaration();
        }

        if ($firstToken->is(T_USE)) {
            $secondToken = $statement->getToken(1);

            if ($secondToken->is(T_FUNCTION)) {
                return new UseFunctionStatement();
            }
            elseif ($secondToken->is(T_CONST)) {
                return new UseConstStatement();
            }

            return new UseClassStatement();
        }

        if ($firstToken->is(T_ATTRIBUTE)) {
            return new AttributeStatement();
        }

        if ($firstToken->is(T_CURLY_BRACKET_CLOSE)) {
            return new BlockCloser();
        }

        if ($statement->containsType(T_CLASS)) {
            return new ClassDeclaration();
        }

        if ($statement->containsType(T_FUNCTION)) {
            return new FunctionDeclaration();
        }
        return new UnknownType();

    }
}
