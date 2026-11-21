<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Tokens, Job};
use Medas\PhpTokenizer\{Statement, Token, TokenType};

/**
 * Moves comments that are at the end of a line in the original code, like in `$a = 1; // Comment`.
 *
 * A comment that starts its statement belongs to the line of the previous statement, and is moved before that
 * statement. A comment inside a statement that spans several lines is moved to the start of its own line within that
 * statement, so it stays with the code it describes.
 *
 * The line breaks after a moved comment were line breaks in the original code between the token before it and the
 * token after it, so they go to the token before it. That way, formatters that look at the original layout, like
 * `KeepOriginalObjectOperatorBreaks`, still see them.
 */
#[Service]
readonly class NoCommentsAtLineEnd extends BaseFormatter
{
    public function priority(): int
    {
        return 1900;
    }

    public function format(Job $job): void
    {
        // Find them all first, as moving them changes which token is before which
        $tokensToMove = array_filter(
            iterator_to_array($job->tree, false),
            fn(Token $token) => $this->isAtLineEnd($token)
        );

        // The last comment moved before each statement, by object id, so several comments keep their order
        $lastPrepended = [];

        foreach ($tokensToMove as $token) {
            $this->keepLineBreaks($token);

            if ($token->isFirstToken()) {
                $this->moveBeforePreviousStatement($token, $lastPrepended);
            }
            elseif (!$this->onlyClosesBefore($token)) {
                $this->moveToStartOfLine($token, $lastPrepended);
            }
        }

        $this->breakLinesBeforeComments($job);
    }

    private function isAtLineEnd(Token $token): bool
    {
        return $token->is(T_COMMENT)
            && $token->previous !== null
            && $token->previous->originalLineBreaksAfter === 0
            && !$token->previous->is([T_OPEN_TAG, TokenType::CURLY_BRACKET_OPEN]);
    }

    private function keepLineBreaks(Token $comment): void
    {
        $previous = $comment->previous;

        $previous->originalLineBreaksAfter = max(
            $previous->originalLineBreaksAfter,
            $comment->originalLineBreaksAfter
        );
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
     * A comment after a line like "}," that only closes brackets is about the code after it, so it stays there, on a
     * line of its own.
     */
    private function onlyClosesBefore(Token $comment): bool
    {
        for ($token = $this->findStartOfLine($comment); $token !== $comment; $token = $token->next) {
            if (!$token->is([
                TokenType::ROUND_BRACKET_CLOSE,
                TokenType::SQUARE_BRACKET_CLOSE,
                TokenType::CURLY_BRACKET_CLOSE,
                TokenType::COMMA
            ])) {
                return false;
            }
        }

        return true;
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

        $keepInStatement = $lineStart !== $statement->firstToken()
            && $this->isInListWithTrailingComma($lineStart);

        $statement->removeToken($token);

        if ($keepInStatement) {
            $statement->insertTokenAfter($token, $lineStart->previous);
        }
        else {
            $this->prependInOrder($statement, $token, $lastPrepended);
        }
    }

    /**
     * Finds the first token of the statement that was on the same line as the comment in the original code.
     */
    private function findStartOfLine(Token $comment): Token
    {
        $lineStart = $comment->previous;

        while ($lineStart->previous?->statement === $comment->statement
                && $lineStart->previous->originalLineBreaksAfter === 0) {
            $lineStart = $lineStart->previous;
        }

        return $lineStart;
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
                    return $previous?->is(TokenType::COMMA) ?? false;
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

    /**
     * A comment inside a statement is on a line of its own, also when the statement isn't split. Otherwise it would
     * end up at the end of a line, and be moved the next time.
     */
    private function breakLinesBeforeComments(Job $job): void
    {
        foreach ($job->tree as $token) {
            if ($token->is(T_COMMENT) && $token->previous?->statement === $token->statement) {
                $token->previous->lineBreakAfter();
            }
        }
    }
}
