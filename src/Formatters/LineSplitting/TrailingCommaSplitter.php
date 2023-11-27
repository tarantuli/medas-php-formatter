<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Tokens, Job};
use Medas\PhpTokenizer\Statement;

#[Service]
readonly class TrailingCommaSplitter extends BaseFormatter
{
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

    private function findSomethingToSplit(Job $job): bool
    {
        foreach ($job->tree->block() as $statement) {
            for ($commaIndex = $statement->tokenCount() - 1; $commaIndex >= 0; --$commaIndex) {
                $token = $statement->getToken($commaIndex);

                if (!$token->is(T_COMMA)) {
                    continue;
                }

                if (!$token->next) {
                    continue;
                }

                $nextText = $token->next->text;

                if (!array_key_exists($nextText, Tokens::OPENING_BRACKETS)) {
                    continue;
                }

                $openerIndex = $this->findOpener(
                    $statement,
                    $commaIndex,
                    Tokens::OPENING_BRACKETS[$nextText],
                    $nextText
                );

                if ($openerIndex === null) {
                    continue;
                }

                $this->splitter->split(
                    $statement,
                    [T_COMMA],
                    true,
                    $openerIndex,
                    $commaIndex + 1,
                    1
                );

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
}
