<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Tokens\{Block, Statement};

#[Service]
class BlockPrinter
{
    private string $indentation;
    private string $lineEnding;
    private int $lineEndingLength;

    public function print(Block $block, string $indentation, string $lineEnding): string
    {
        $this->indentation = $indentation;
        $this->lineEnding = $lineEnding;
        $this->lineEndingLength = strlen($lineEnding);

        ob_start();

        $this->printBlock($block);

        // A new line is required at the end
        if ($this->lineEndingLength === 0) {
            echo "\n";
        }

        return ob_get_clean();
    }

    private function printBlock(Block $block): void
    {
        foreach ($block as $statement) {
            $this->printStatement($statement);
        }
    }

    private function printStatement(Statement $statement): void
    {
        $this->printIndentation($statement);

        foreach ($statement as $token) {
            echo $token->text;

            if ($token->lineBreakAfter) {
                echo $this->lineEnding;
                $this->printIndentation($statement);
            }
            elseif ($token->spaceAfter && !$token->isLastToken()) {
                echo ' ';
            }
        }

        echo $this->lineEnding;

        // A line ending is required after some tokens
        if ($this->lineEndingLength === 0 && $statement->lastToken()->is([T_OPEN_TAG, T_COMMENT])) {
            echo "\n";
        }

        if ($statement->blankLineAfter) {
            echo $this->lineEnding;
        }
    }

    private function printIndentation(Statement $statement): void
    {
        echo str_repeat(
            $this->indentation,
            $statement->block->depth + $statement->additionalDepth
        );
    }
}
