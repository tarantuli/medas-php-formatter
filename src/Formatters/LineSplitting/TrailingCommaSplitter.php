<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Token, TokenType};

#[Service]
readonly class TrailingCommaSplitter extends BaseFormatter
{
    private const array CLOSING_BRACKETS = [
        TokenType::ROUND_BRACKET_CLOSE,
        TokenType::SQUARE_BRACKET_CLOSE,
        TokenType::CURLY_BRACKET_CLOSE,
    ];

    public function __construct(
        private Helpers\StatementSplitter $splitter,
    )
    {
    }

    public function priority(): int
    {
        return 700;
    }

    public function format(Job $job): void
    {
        whileTrue(fn() => $this->findSomethingToSplit($job));
    }

    /**
     * Splits a list with a trailing comma, like "[1, 2,]" or "foo($a, $b,)", over several lines.
     */
    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            for ($commaIndex = $statement->tokenCount() - 1; $commaIndex >= 0; --$commaIndex) {
                $comma = $statement->getToken($commaIndex);
                $opener = $this->openerOfList($comma);

                // The list has to start in this statement, which isn't so for the arms of a match
                if ($opener?->statement !== $statement) {
                    continue;
                }

                $this->splitter->split(
                    $statement,
                    [TokenType::COMMA],
                    true,
                    $statement->getIndex($opener),
                    $commaIndex + 1,
                    1
                );

                return true;
            }
        }

        return false;
    }

    /**
     * The opening bracket of the list that a trailing comma ends, if the token is one.
     */
    private function openerOfList(Token $token): Token|null
    {
        if (!$token->is(TokenType::COMMA)) {
            return null;
        }

        $closer = $token->next;

        if (!$closer?->is(self::CLOSING_BRACKETS)) {
            return null;
        }

        return $closer->matchingBracket;
    }
}
