<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlockCloser;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\PhpOpenTag;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseConstStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseFunctionStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12BlankLines implements BlockFormatter
{
    public function __construct(
        private BlankLineAdder      $blankLineAdder,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(Block $block): void
    {
        $this->blankLineAdder->afterTypes($block, [
            PhpOpenTag::class,
            DeclareStatement::class,
            NamespaceDeclaration::class,
            UseClassStatement::class,
            UseFunctionStatement::class,
            UseConstStatement::class,
        ]);

        $this->additionalLines($block);
    }

    private function additionalLines(Block $block): void
    {
        foreach ($block as $statement) {
            if ($statement instanceof Block) {
                $this->additionalLines($statement);
                continue;
            }

            $type = $this->typeFinder->for($statement);

            if ($type instanceof ClassDeclaration || $type instanceof FunctionDeclaration) {
                $statement->getToken(-2)->lineBreakAfter = true;
            }

            if ($type instanceof BlockCloser && !$statement->lastToken()->is(T_CURLY_BRACKET_OPEN)) {
                $statement->blankLineAfter = true;
            }

            if ($statement === $block->lastStatement()) {
                $statement->blankLineAfter = false;
            }
        }
    }
}
