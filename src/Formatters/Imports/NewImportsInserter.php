<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Formatters\Imports;

use Medas\PhpBeautifier\Tokens\ClassAnalyser\FqnProperties;
use Medas\PhpBeautifier\Tokens\Contexts\GlobalScope;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\PhpOpenTag;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class NewImportsInserter
{
    public function __construct(
        private FqnProperties       $fqnProperties,
        private StatementTypeFinder $statementTypeFinder,
    )
    {
    }

    public function insert(TokenTree $tree, ReferencesAndImports $referencesAndImports): void
    {
        $this->removeExistingImportStatements($tree);
        // Sort the new imports in reverse order
        krsort($referencesAndImports->imports);

        $after = $this->findImportInsertionSpot($tree);

        $baseToken = new Token(ord(';'), ';');
        $baseToken->context = new GlobalScope();
        $baseToken->inString = false;
        $baseToken->inAttribute = false;

        foreach ($referencesAndImports->imports as $fqn => $alias) {
            $statement = $tree->block()->appendNewStatement();
            $statement->appendToken((clone $baseToken)->id(T_USE)->text('use'));
            $statement->appendToken((clone $baseToken)->id(T_NAME_QUALIFIED)->text(substr($fqn, 1)));

            if ($alias !== $this->fqnProperties->getLastPart($fqn)) {
                $statement->appendToken((clone $baseToken)->id(T_AS)->text('as'));
                $statement->appendToken((clone $baseToken)->id(T_STRING)->text($alias));
            }

            $statement->appendToken((clone $baseToken)->id(ord(';'))->text(';'));
            $tree->block()->moveStatementAfter($statement, $after);
        }
    }

    private function removeExistingImportStatements(TokenTree $tree): void
    {
        foreach ($tree->block() as $statement) {
            if ($this->statementTypeFinder->for($statement) instanceof UseClassStatement) {
                $tree->block()->removeStatement($statement, true);
            }
        }
    }

    private function findImportInsertionSpot(TokenTree $tree): Statement
    {
        if (null !== $after = $this->getStatementByType($tree, NamespaceDeclaration::class)) {
            return $after;
        }

        if (null !== $after = $this->getStatementByType($tree, DeclareStatement::class)) {
            return $after;
        }

        return $this->getStatementByType($tree, PhpOpenTag::class);
    }

    private function getStatementByType(TokenTree $tree, string $type): Statement|null
    {
        foreach ($tree->block() as $statement) {
            if ($this->statementTypeFinder->for($statement) instanceof $type) {
                return $statement;
            }
        }

        return null;
    }
}
