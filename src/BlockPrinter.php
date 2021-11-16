<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\Statement;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class BlockPrinter
{
    private string $indentation;
    private string $lineEnding;

    public function print(Block $block, string $indentation, string $lineEnding): string
    {
        $this->indentation = $indentation;
        $this->lineEnding = $lineEnding;

        ob_start();

        $this->printBlock($block);

        return ob_get_clean();
    }

    private function printBlock(Block $block)
    {
        foreach ($block as $blockOrStatement) {
            if ($blockOrStatement instanceof Statement) {
                $this->printStatement($blockOrStatement);
            }
            else {
                $this->printBlock($blockOrStatement);
            }
        }
    }

    private function printStatement(Statement $statement)
    {
        echo str_repeat($this->indentation, $statement->block->depth);

        foreach ($statement as $token) {
            echo $token->text;
        }

        echo $this->lineEnding;
    }
}
