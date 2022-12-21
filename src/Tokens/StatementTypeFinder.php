<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class StatementTypeFinder
{
    public function __construct(
        private readonly TokenGroups $tokenGroups,
    )
    {
    }

    public function for(Statement $statement): StatementTypes\StatementType
    {
        return $statement->type(fn() => $this->determineType($statement));
    }

    private function determineType(Statement $statement): StatementTypes\StatementType
    {
        $firstToken = $statement->firstToken();
        $secondToken = $statement->getToken(1);

        if (null === $firstToken) {
            return new StatementTypes\BlankLine();
        }

        if ($firstToken->is(T_OPEN_TAG)) {
            return new StatementTypes\PhpOpenTag();
        }

        if ($firstToken->is(T_DECLARE)) {
            return new StatementTypes\DeclareStatement();
        }

        if ($firstToken->is($this->tokenGroups->comments())) {
            return new StatementTypes\Comment();
        }

        if ($firstToken->is(T_NAMESPACE)) {
            return new StatementTypes\NamespaceDeclaration();
        }

        if ($firstToken->is(T_USE)) {
            if ($secondToken->is(T_FUNCTION)) {
                return new StatementTypes\UseFunctionStatement();
            }
            elseif ($secondToken->is(T_CONST)) {
                return new StatementTypes\UseConstStatement();
            }

            return new StatementTypes\UseClassStatement();
        }

        if ($firstToken->is(T_ATTRIBUTE)) {
            return new StatementTypes\AttributeStatement();
        }

        if ($firstToken->is(T_CURLY_BRACKET_CLOSE)) {
            return new StatementTypes\BlockCloser();
        }

        if ($statement->containsType(T_CLASS)) {
            return new StatementTypes\ClassDeclaration();
        }

        if ($statement->containsType(T_CONST)) {
            return new StatementTypes\ClassConstDeclaration();
        }

        if ($statement->containsType(T_FUNCTION)) {
            return new StatementTypes\FunctionDeclaration();
        }

        if ($statement->containsType($this->tokenGroups->visibilityKeywords())) {
            // It's not a class const or class method, those were found before
            return new StatementTypes\ClassPropertyDeclaration();
        }

        if ($firstToken->is($this->tokenGroups->controlKeywords())) {
            return new StatementTypes\ControlStatement();
        }

        if ($firstToken->is(T_CASE)) {
            return new StatementTypes\SwitchBranch();
        }

        if ($firstToken->is(T_DEFAULT) && !$secondToken->is(T_DOUBLE_ARROW)) {
            // A default followed by a double arrow is a match branch
            return new StatementTypes\SwitchBranch();
        }

        if ($firstToken->is(T_RETURN)) {
            return new StatementTypes\ReturnStatement();
        }

        if ($firstToken->is(T_THROW)) {
            return new StatementTypes\ThrowStatement();
        }

        return new StatementTypes\GenericStatement();
    }
}
