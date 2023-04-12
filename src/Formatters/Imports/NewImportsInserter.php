<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\PhpFormatter\Tokens\ClassAnalyser\FqnProperties;
use Medas\PhpFormatter\Tokens\Contexts\GlobalScope;
use Medas\PhpFormatter\Tokens\Statement;
use Medas\PhpFormatter\Tokens\StatementTypeFinder;
use Medas\PhpFormatter\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\PhpOpenTag;
use Medas\PhpFormatter\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpFormatter\Tokens\Token;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Service;

#[Service]
class NewImportsInserter
{
    public function __construct(
        private readonly FqnProperties       $fqnProperties,
        private readonly StatementTypeFinder $statementTypeFinder,
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
        $baseToken->context = GlobalScope::instance();
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
        foreach ($tree->statements() as $statement) {
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
        foreach ($tree->statements() as $statement) {
            if ($this->statementTypeFinder->for($statement) instanceof $type) {
                return $statement;
            }
        }

        return null;
    }
}
