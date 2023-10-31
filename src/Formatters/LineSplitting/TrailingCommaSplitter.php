<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class TrailingCommaSplitter extends BaseFormatter
{
    private const BRACKETS = [
        T_SQUARE_BRACKET_CLOSE => T_SQUARE_BRACKET_OPEN,
        T_CURLY_BRACKET_CLOSE => T_CURLY_BRACKET_OPEN,
        T_ROUND_BRACKET_CLOSE => T_ROUND_BRACKET_OPEN,
    ];

    private const CLOSERS = [
        T_SQUARE_BRACKET_CLOSE,
        T_CURLY_BRACKET_CLOSE,
        T_ROUND_BRACKET_CLOSE,
    ];

    public function format(Job $job): void
    {
        do {
            $foundSomething = $this->findSomethingToSplit($job);
        } while ($foundSomething);
    }

    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            foreach ($statement as $commaIndex => $token) {
                if (!$token->is(T_COMMA)) {
                    continue;
                }

                if (!$token->next) {
                    continue;
                }

                $nextText = $token->next->text;

                if (!array_key_exists($nextText, self::BRACKETS)) {
                    continue;
                }

                $openerIndex = $this->findOpener($statement, $commaIndex, self::BRACKETS[$nextText], $nextText);

                if ($openerIndex === null) {
                    continue;
                }

                $this->splitStatementByComma($statement, $openerIndex, $commaIndex + 1);

                return true;
            }
        }

        return false;
    }

    private function findOpener(Statement $statement, int $index, string $opener, string $closer): int|null
    {
        $depth = 0;

        for ($openerIndex = $index - 1; $index >= 0; --$openerIndex) {
            if (null === $token = $statement->getToken($openerIndex)) {
                return null;
            }

            $text = $token->text;

            if ($text === $closer) {
                ++$depth;
            }

            if ($text === $opener && $depth === 0) {
                return $openerIndex;
            }

            if ($text === $opener) {
                --$depth;
            }
        }

        return null;
    }

    private function splitStatementByComma(Statement $statement, int $openerIndex, int $closerIndex): void
    {
        $this->extractTrailingTokens($statement, $statement->tokenCount() - 1, $closerIndex);

        $currentStatement = null;
        $depth = 0;

        for ($i = $closerIndex - 1; $i > $openerIndex; --$i) {
            $token = $statement->getToken($i);
            $statement->removeToken($token);

            $text = $token->text;

            if (!$token->inAttribute && in_array($text, self::CLOSERS)) {
                ++$depth;
            }

            if ($text === T_COMMA && $depth === 0) {
                $currentStatement = new Statement($statement->block);
                $currentStatement->rootStatement = $statement;
                $statement->block->insertStatementAfter($currentStatement, $statement);
                $currentStatement->additionalDepth++;
            }

            if (!$token->inAttribute && in_array($text, self::BRACKETS)) {
                --$depth;
            }

            $currentStatement->prependToken($token);
        }
    }

    private function extractTrailingTokens(Statement $statement, int $lastIndex, int $closerIndex): void
    {
        $finalStatement = new Statement($statement->block);
        $finalStatement->rootStatement = $statement;
        $statement->block->insertStatementAfter($finalStatement, $statement);

        for ($i = $lastIndex; $i >= $closerIndex; --$i) {
            $token = $statement->getToken($i);
            $statement->removeToken($token);
            $finalStatement->prependToken($token);
        }
    }
}
