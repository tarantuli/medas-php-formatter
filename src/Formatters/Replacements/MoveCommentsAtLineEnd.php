<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Tokens, Job, Preformatters\NoCommentsAtLineEnd};
use Medas\PhpTokenizer\{Statement, Token};

/**
 * This formatter processes the tokens marked by the preformatter NoCommentsAtLineEnd. That class also registers this
 * class, so no need to add it manually to Settings.
 *
 * A comment that starts its statement belongs to the line of the previous statement, and is moved before that
 * statement. A comment inside a statement that spans several lines is moved to the start of its own line within that
 * statement, so it stays with the code it describes.
 */
#[Service]
readonly class MoveCommentsAtLineEnd extends BaseFormatter
{
    public function __construct(
        private NoCommentsAtLineEnd $preformatter,
    )
    {
    }

    public function priority(): int
    {
        return 1900;
    }

    public function format(Job $job): void
    {
        if (!$this->preformatter->tokensToMove()) {
            return;
        }

        $tokensToMove = array_filter(
            iterator_to_array($job->tree, false),
            fn(Token $token) => in_array($token, $this->preformatter->tokensToMove(), true)
        );

        // The last comment moved before each statement, by object id, so several comments keep their order
        $lastPrepended = [];

        foreach ($tokensToMove as $token) {
            if ($token->isFirstToken()) {
                $this->moveBeforePreviousStatement($token, $lastPrepended);
            }
            else {
                $this->moveToStartOfLine($token, $lastPrepended);
            }
        }
    }

    /** @param Token[] $lastPrepended */
    private function moveBeforePreviousStatement(Token $token, array &$lastPrepended): void
    {
        $statement = $token->statement;

        $statement->removeToken($token);

        $previousStatement = $statement->previous();

        if ($previousStatement === null) {
            $previousStatement = new Statement($statement->block);

            $statement->block->insertStatementBefore($previousStatement, $statement);
        }

        $this->prependInOrder($previousStatement, $token, $lastPrepended);

        // A comment that was the whole statement, like the one in "default: // Comment", leaves it empty
        if ($statement->tokenCount() === 0) {
            $statement->block->removeStatement($statement);
        }
    }

    /**
     * Only a list with a trailing comma is certain to be split over multiple lines (by `TrailingCommaSplitter`). In
     * any other case, the comment is moved before the whole statement, so it doesn't end up in the middle of a line.
     *
     * @param Token[] $lastPrepended
     */
    private function moveToStartOfLine(Token $token, array &$lastPrepended): void
    {
        $statement = $token->statement;
        $lineStart = $this->findStartOfLine($token);
        $keepInStatement = $lineStart !== $statement->firstToken() && $this->isInListWithTrailingComma($lineStart);

        $statement->removeToken($token);

        if ($keepInStatement) {
            $statement->insertTokenAfter($token, $lineStart->previous);
        }
        else {
            $this->prependInOrder($statement, $token, $lastPrepended);
        }
    }

    /** @param Token[] $lastPrepended */
    private function prependInOrder(Statement $statement, Token $token, array &$lastPrepended): void
    {
        $id = spl_object_id($statement);

        if (isset($lastPrepended[$id])) {
            $statement->insertTokenAfter($token, $lastPrepended[$id]);
        }
        else {
            $statement->prependToken($token);
        }

        $lastPrepended[$id] = $token;
    }

    private function isInListWithTrailingComma(Token $lineStart): bool
    {
        $opener = $this->findEnclosingOpener($lineStart);

        if ($opener === null) {
            return false;
        }

        $depth = 0;
        $previous = null;

        for ($token = $opener->next; $token?->statement === $lineStart->statement; $token = $token->next) {
            if ($this->isOpeningBracket($token)) {
                ++$depth;
            }
            elseif ($this->isClosingBracket($token)) {
                if ($depth === 0) {
                    return $previous?->is(T_COMMA) ?? false;
                }

                --$depth;
            }

            if (!$token->is([T_COMMENT, T_DOC_COMMENT])) {
                $previous = $token;
            }
        }

        return false;
    }

    private function findEnclosingOpener(Token $lineStart): Token|null
    {
        $depth = 0;

        for ($token = $lineStart->previous; $token?->statement === $lineStart->statement; $token = $token->previous) {
            if ($this->isClosingBracket($token)) {
                ++$depth;
            }
            elseif ($this->isOpeningBracket($token)) {
                if ($depth === 0) {
                    return $token;
                }

                --$depth;
            }
        }

        return null;
    }

    private function isOpeningBracket(Token $token): bool
    {
        return !$token->inString && $token->is(Tokens::OPENING_BRACKETS);
    }

    private function isClosingBracket(Token $token): bool
    {
        return !$token->inString && $token->is(Tokens::CLOSING_BRACKETS);
    }

    /**
     * Finds the first token of the statement that was on the same line as the comment in the original code.
     */
    private function findStartOfLine(Token $comment): Token
    {
        $lineStart = $comment->previous;

        while ($lineStart->previous?->statement === $comment->statement
                && $lineStart->previous->line === $comment->line) {
            $lineStart = $lineStart->previous;
        }

        return $lineStart;
    }
}
