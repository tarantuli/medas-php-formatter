<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\BlockCloser;
use Medas\PhpBeautifier\Tokens\StatementTypes\ClassDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\DeclareStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\FunctionDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\NamespaceDeclaration;
use Medas\PhpBeautifier\Tokens\StatementTypes\StatementType;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseClassStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseConstStatement;
use Medas\PhpBeautifier\Tokens\StatementTypes\UseFunctionStatement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Psr12BlankLines implements BlockFormatter
{
    private const BLANK_LINE_AFTER_GROUP = [
        DeclareStatement::class,
        NamespaceDeclaration::class,
        UseClassStatement::class,
        UseFunctionStatement::class,
        UseConstStatement::class,
        BlockCloser::class,
    ];
    private ?Statement $previousStatement = null;
    private ?StatementType $previousType = null;

    public function __construct(private StatementTypeFinder $typeFinder)
    {
    }

    public function format(Block $block): void
    {
        $this->addBlankLines($block);
    }

    private function addBlankLines(Block $block): void
    {
        foreach ($block as $statement) {
            if ($statement instanceof Block) {
                $this->addBlankLines($statement);
                continue;
            }

            $type = $this->typeFinder->for($statement);

            foreach (self::BLANK_LINE_AFTER_GROUP as $groupType) {
                if ($type instanceof $groupType) {
                    $statement->blankLineAfter = true;

                    if ($this->previousType instanceof $groupType) {
                        $this->previousStatement->blankLineAfter = false;
                    }
                }
            }

            if ($type instanceof ClassDeclaration || $type instanceof FunctionDeclaration) {
                $statement->getToken(-2)->lineBreakAfter = true;
            }

            $this->previousStatement = $statement;
            $this->previousType = $type;
        }
    }
}
