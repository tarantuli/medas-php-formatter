<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Block, Statement};

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

        $lastToken = $statement->lastToken();

        foreach ($statement as $token) {
            echo $token->text;

            if ($this->lineEndingLength === 0 && $token->is([T_OPEN_TAG, T_COMMENT])) {
                // A line ending is required after some tokens
                echo "\n";
            }

            if ($token->lineBreakAfter && $token !== $lastToken) {
                echo $this->lineEnding;

                $this->printIndentation($statement);
            }
            elseif ($token->spaceAfter && !$token->isLastToken()) {
                echo ' ';
            }

            if ($token->extraSpacesAfter) {
                echo str_repeat(' ', $token->extraSpacesAfter);
            }
        }

        if (!$statement->lastToken()->is(T_INLINE_HTML)) {
            echo $this->lineEnding;
        }

        if ($statement->blankLineAfter) {
            echo $this->lineEnding;
        }
    }

    private function printIndentation(Statement $statement): void
    {
        echo str_repeat($this->indentation, $statement->block->depth + $statement->additionalDepth);
    }
}
