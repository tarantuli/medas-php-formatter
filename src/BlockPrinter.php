<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\{Block, Statement, Token};

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

        if ($statement->extraSpacesBefore > 0) {
            echo str_repeat(' ', $statement->extraSpacesBefore);
        }

        $lastToken = $statement->lastToken();

        foreach ($statement as $token) {
            echo $this->tokenText($token, $statement);

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
        echo $this->getIndentation($statement);
    }

    private function getIndentation(Statement $statement): string
    {
        return str_repeat($this->indentation, $statement->block->depth + $statement->additionalDepth);
    }

    /**
     * Doc comments and block comments keep their original indentation and line endings in the token text. Their lines
     * that start with `*` are re-indented to the statement, and all their line endings become the document's.
     */
    private function tokenText(Token $token, Statement $statement): string
    {
        $isBlockComment = $token->is(T_DOC_COMMENT) || ($token->is(T_COMMENT) && str_starts_with($token->text, '/*'));

        if (!$isBlockComment || $this->lineEndingLength === 0 || !preg_match('/\R/', $token->text)) {
            return $token->text;
        }

        $indentation = $this->getIndentation($statement);
        $lines = preg_split('/\R/', $token->text);

        foreach ($lines as $index => $line) {
            $trimmed = ltrim($line);

            if ($index > 0 && str_starts_with($trimmed, '*')) {
                $lines[$index] = $indentation . ' ' . $trimmed;
            }
        }

        return implode($this->lineEnding, $lines);
    }
}
