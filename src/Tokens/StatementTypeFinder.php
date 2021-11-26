<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\PhpBeautifier\Tokens\StatementTypes\AttributeStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlankLine;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlockCloser;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassConstDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassPropertyDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\Comment;
use Medas\PhpBeautifier\Tokens\StatementTypes\ControlStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\GenericStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\PhpOpenTag;
use Medas\PhpBeautifier\Tokens\StatementTypes\StatementType;
use Medas\PhpBeautifier\Tokens\StatementTypes\SwitchBranch;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseConstStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseFunctionStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class StatementTypeFinder
{
    public function __construct(private TokenGroups $tokenGroups)
    {
    }

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
        $secondToken = $statement->getToken(1);

        if (null === $firstToken) {
            return new BlankLine();
        }

        if ($firstToken->is(T_OPEN_TAG)) {
            return new PhpOpenTag();
        }

        if ($firstToken->is(T_DECLARE)) {
            return new DeclareStatement();
        }

        if ($firstToken->is(T_COMMENT)) {
            return new Comment();
        }

        if ($firstToken->is(T_NAMESPACE)) {
            return new NamespaceDeclaration();
        }

        if ($firstToken->is(T_USE)) {
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

        if ($statement->containsType(T_CONST)) {
            return new ClassConstDeclaration();
        }

        if ($statement->containsType(T_FUNCTION)) {
            return new FunctionDeclaration();
        }

        if ($statement->containsType($this->tokenGroups->visibilityKeywords())) {
            // It's not a class const or class method, those were found before
            return new ClassPropertyDeclaration();
        }

        if ($firstToken->is($this->tokenGroups->controlKeywords())) {
            return new ControlStatement();
        }

        if ($firstToken->is(T_CASE)) {
            return new SwitchBranch();
        }

        if ($firstToken->is(T_DEFAULT) && !$secondToken->is(T_DOUBLE_ARROW)) {
            // A default followed by a double arrow is a match branch
            return new SwitchBranch();
        }

        return new GenericStatement();
    }
}
