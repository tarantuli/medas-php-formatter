<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Tokens\StatementTypeFinder;
use Medas\PhpFormatter\Tokens\StatementTypes\BlockCloser;
use Medas\PhpFormatter\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpFormatter\Tokens\StatementTypes\PhpOpenTag;
use Medas\PhpFormatter\Tokens\StatementTypes\SwitchBranch;
use Medas\PhpFormatter\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\UseConstStatement;
use Medas\PhpFormatter\Tokens\StatementTypes\UseFunctionStatement;
use Medas\PhpFormatter\Tokens\TokenTree;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12BlankLines extends BaseFormatter
{
    public function __construct(
        private BlankLineAdder      $blankLineAdder,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(TokenTree $tree): void
    {
        $this->blankLineAdder->afterTypes($tree, [
            PhpOpenTag::class,
            DeclareStatement::class,
            NamespaceDeclaration::class,
            UseClassStatement::class,
            UseFunctionStatement::class,
            UseConstStatement::class,
        ]);

        $this->additionalLines($tree);
        $this->addSwitchIndentation($tree);
    }

    private function additionalLines(TokenTree $tree): void
    {
        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);

            if ($type instanceof ClassDeclaration) {
                $statement->getToken(-2)->lineBreakAfter = true;
            }

            if ($type instanceof FunctionDeclaration) {
                if ($statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                    // It's a non-abstract function declaration
                    $statement->getToken(-2)->lineBreakAfter = true;
                }
                else {
                    // It's an abstract function declaration
                    $statement->blankLineAfter = true;
                }
            }

            if ($type instanceof BlockCloser && !$statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                $statement->blankLineAfter = true;
            }

            // No blank line needed before a case or default branch
            if ($type instanceof SwitchBranch && $previousStatement = $statement->previous()) {
                $previousStatement->blankLineAfter = false;

            }

            if ($statement === $statement->block->lastStatement()) {
                $statement->blankLineAfter = false;
            }
        }
    }

    private function addSwitchIndentation(TokenTree $tree): void
    {
        $switchDepths = [];

        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);

            if ($statement->firstToken()->is(T_CURLY_BRACKET_CLOSE)) {
                $switchDepths = array_filter($switchDepths, fn($depth) => $depth !== $statement->block->depth + 1);
            }

            // Add additional depth levels to each statement equal to the number of open switch blocks
            // decreased by one if this statement itself is a case or default statement
            $statement->additionalDepth += count($switchDepths) - $type instanceof SwitchBranch;

            if ($statement->firstToken()->is(T_SWITCH)) {
                $switchDepths[] = $statement->block->depth + 1;
            }
        }
    }
}
