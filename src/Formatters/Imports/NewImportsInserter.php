<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Imports;

use Medas\Core\Attributes\Service;
use Medas\PhpClassAnalysis\FqnProperties;
use Medas\PhpTokenizer\{
    Block,
    BlockType,
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
    private const int GENERIC_TOKEN_ID = 123;

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
        $comments = $this->removeExistingImportStatements($tree);

        $after = $this->findImportInsertionSpot($tree);
        $groupedImports = $this->importGrouper->group($referencesAndImports);

        // Sort the new imports in reverse order, so we can insert statements one by one below the insertion spot
        uksort($groupedImports, function (string $a, string $b) {
            // Replace backslases by spaces, so longer parts sort later (otherwise, backslash sorts after text)
            return -1 * (str_replace('\\', ' ', $a) <=> str_replace('\\', ' ', $b));
        });

        // Because of the reverse order, the statement inserted last is the first import
        $firstImport = null;

        foreach ($groupedImports as $fqn => $alias) {
            $statement = $this->insertAtSpot($tree, $after, $firstImport);

            $this->addImportToStatement($statement, $fqn, $alias);

            $firstImport = $statement;
        }

        $this->restoreComments($tree, $comments, $firstImport, $after);
    }

    /**
     * Inserts a new statement at the start of the imports: right after the insertion spot, or, after "namespace X {",
     * at the start of the namespace block.
     *
     * @param Statement|null $firstImport The import inserted before, which the new statement goes in front of
     */
    private function insertAtSpot(TokenTree $tree, Statement $after, Statement|null $firstImport): Statement
    {
        $namespaceBlock = $this->namespaceBlock($tree, $after);

        if ($namespaceBlock === null) {
            $statement = $after->block->appendNewStatement();
            $after->block->moveStatementAfter($statement, $after);

            return $statement;
        }

        $before = $firstImport ?? $this->firstStatementIn($tree, $namespaceBlock);
        $statement = $namespaceBlock->appendNewStatement();

        if ($before !== null) {
            $namespaceBlock->removeStatement($statement);
            $namespaceBlock->insertStatementBefore($statement, $before);
        }

        return $statement;
    }

    /**
     * The block of "namespace X { ... }", if the insertion spot opens one.
     */
    private function namespaceBlock(TokenTree $tree, Statement $after): Block|null
    {
        if ($after->lastToken()?->opensBlock !== BlockType::Namespace) {
            return null;
        }

        foreach ($tree->statements() as $statement) {
            if ($statement->block->opener === $after) {
                return $statement->block;
            }
        }

        return null;
    }

    private function firstStatementIn(TokenTree $tree, Block $block): Statement|null
    {
        foreach ($tree->statements() as $statement) {
            if ($statement->block === $block) {
                return $statement;
            }
        }

        return null;
    }

    /**
     * Removes all class import statements.
     *
     * @return Token[] The comments in front of the removed statements, in their original order
     */
    private function removeExistingImportStatements(TokenTree $tree): array
    {
        $comments = [];

        foreach ($tree->statements() as $statement) {
            if (!$this->statementTypeFinder->for($statement) instanceof UseClassStatement) {
                continue;
            }

            foreach ($statement as $token) {
                if (!$token->is([T_COMMENT, T_DOC_COMMENT])) {
                    break;
                }

                $comments[] = $token;
            }

            $statement->block->removeStatement($statement, true);
        }

        return $comments;
    }

    /**
     * The imports are regrouped and sorted, so a comment can't stay with the import it was in front of. All comments
     * are put in front of the first import instead, or after the insertion spot if no imports are left.
     *
     * @param Token[] $comments
     */
    private function restoreComments(
        TokenTree      $tree,
        array          $comments,
        Statement|null $firstImport,
        Statement      $after
    ): void
    {
        if ($comments === []) {
            return;
        }

        if ($firstImport === null) {
            $firstImport = $this->insertAtSpot($tree, $after, null);
            $firstImport->blankLineAfter();
        }

        foreach (array_reverse($comments) as $comment) {
            $firstImport->prependToken($comment);
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

    private function addImportToStatement(Statement $statement, int|string $fqn, array|string $alias): void
    {
        $statement->appendToken((clone $this->baseToken)->id(T_USE)->text('use'));
        $statement->appendToken((clone $this->baseToken)->id(T_NAME_QUALIFIED)->text(substr($fqn, 1)));

        if (is_array($alias)) {
            $this->appendGroupedAliases($statement, $alias);
        }
        elseif ($alias !== $this->fqnProperties->getLastPart($fqn)) {
            $this->appendAlias($statement, $alias);
        }

        $statement->appendToken(clone $this->baseToken);
    }

    private function appendGroupedAliases(Statement $statement, array $aliases): void
    {
        $statement->appendToken((clone $this->baseToken)->id(T_NS_SEPARATOR)->text('\\'));
        $statement->appendToken((clone $this->baseToken)->id(self::GENERIC_TOKEN_ID)->text('{'));

        $isFirst = true;

        ksort($aliases, SORT_STRING | SORT_FLAG_CASE);

        foreach ($aliases as $subPath => $alias) {
            if (!$isFirst) {
                $statement->appendToken((clone $this->baseToken)->id(self::GENERIC_TOKEN_ID)->text(','));
            }

            $statement->appendToken((clone $this->baseToken)->id(T_STRING)->text($subPath));

            if ($this->fqnProperties->getLastPart($subPath) !== $alias) {
                $this->appendAlias($statement, $alias);
            }

            $isFirst = false;
        }

        $statement->appendToken((clone $this->baseToken)->id(self::GENERIC_TOKEN_ID)->text('}'));
    }

    private function appendAlias(Statement $statement, string $alias): void
    {
        $statement->appendToken((clone $this->baseToken)->id(T_AS)->text('as'));
        $statement->appendToken((clone $this->baseToken)->id(T_STRING)->text($alias));
    }
}
