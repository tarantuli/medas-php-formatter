<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\BlockFormatters;

use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\PhpBeautifier\Tokens\StatementTypeFinder;
use Medas\PhpBeautifier\Tokens\StatementTypes\StatementType;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlankLineAdder
{
    private ?Statement $previousStatement = null;
    private ?StatementType $previousType = null;

    public function __construct(private StatementTypeFinder $typeFinder)
    {
    }

    public function afterTypes(Block $block, array $afterTypes): void
    {
        foreach ($block as $statement) {
            $type = $this->typeFinder->for($statement);

            foreach ($afterTypes as $groupType) {
                if ($type instanceof $groupType) {
                    $statement->blankLineAfter = true;

                    if ($this->previousType instanceof $groupType) {
                        $this->previousStatement->blankLineAfter = false;
                    }
                }
            }

            $this->previousStatement = $statement;
            $this->previousType = $type;
        }
    }

    public function beforeTypes(Block $block, array $beforeTypes): void
    {
        foreach ($block as $statement) {
            $type = $this->typeFinder->for($statement);

            foreach ($beforeTypes as $groupType) {
                if ($type instanceof $groupType
                    && $this->previousStatement->block === $statement->block) {
                    $this->previousStatement->blankLineAfter = true;
                }
            }

            $this->previousStatement = $statement;
        }
    }
}
