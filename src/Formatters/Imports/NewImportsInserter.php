<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\FqnProperties;
use Medas\PhpTokenizer\{
    Contexts\GlobalScope,
    Statement,
    StatementTypeFinder,
    StatementTypes\DeclareStatement,
    StatementTypes\NamespaceDeclaration,
    StatementTypes\PhpOpenTag,
    StatementTypes\UseClassStatement,
    Token,
    TokenTree
};

#[Service]
readonly class NewImportsInserter
{
    private Token $baseToken;

    public function __construct(
        private FqnProperties          $fqnProperties,
        private Grouping\ImportGrouper $importGrouper,
        private StatementTypeFinder    $statementTypeFinder,
    )
    {
        $this->baseToken = new Token(ord(';'), ';');
        $this->baseToken->context = GlobalScope::instance();
        $this->baseToken->inString = false;
        $this->baseToken->inAttribute = false;
    }

    public function insert(TokenTree $tree, ReferencesAndImports $referencesAndImports): void
    {
        $this->removeExistingImportStatements($tree);

        $after = $this->findImportInsertionSpot($tree);
        $groupedImports = $this->importGrouper->group($referencesAndImports);

        // Sort the new imports in reverse order, so we can insert statements one by one below the insertion spot
        uksort($groupedImports, function (string $a, string $b) {
            // Replace backslases by spaces, so longer parts sort later (otherwise, backslash sorts after text)
            return  -1 * (str_replace('\\', ' ', $a) <=> str_replace('\\', ' ', $b));
        });


        foreach ($groupedImports as $fqn => $alias) {
            $statement = $tree->block()->appendNewStatement();

            $this->processGroupedImport($statement, $fqn, $alias);

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

    private function processGroupedImport(Statement $statement, int|string $fqn, mixed $alias): void
    {
        $statement->appendToken((clone $this->baseToken)->id(T_USE)->text('use'));
        $statement->appendToken((clone $this->baseToken)->id(T_NAME_QUALIFIED)->text(substr($fqn, 1)));

        if (is_array($alias)) {
            $statement->appendToken((clone $this->baseToken)->id(T_NS_SEPARATOR)->text('\\'));
            $statement->appendToken((clone $this->baseToken)->id(123)->text('{'));

            $isFirst = true;

            ksort($alias, SORT_STRING | SORT_FLAG_CASE);

            foreach ($alias as $subPath => $subAlias) {
                if (!$isFirst) {
                    $statement->appendToken((clone $this->baseToken)->id(123)->text(','));
                }

                $statement->appendToken((clone $this->baseToken)->id(T_STRING)->text($subPath));

                if ($this->fqnProperties->getLastPart($subPath) !== $subAlias) {
                    $statement->appendToken((clone $this->baseToken)->id(T_AS)->text('as'));
                    $statement->appendToken((clone $this->baseToken)->id(T_STRING)->text($subAlias));
                }

                $isFirst = false;
            }

            $statement->appendToken((clone $this->baseToken)->id(123)->text('}'));
        }
        else {
            if ($alias !== $this->fqnProperties->getLastPart($fqn)) {
                $statement->appendToken((clone $this->baseToken)->id(T_AS)->text('as'));
                $statement->appendToken((clone $this->baseToken)->id(T_STRING)->text($alias));
            }
        }

        $statement->appendToken(clone $this->baseToken);
    }
}
