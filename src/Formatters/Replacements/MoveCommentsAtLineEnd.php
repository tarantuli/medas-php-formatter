<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job, Preformatters\NoCommentsAtLineEnd};
use Medas\PhpTokenizer\Statement;

/**
 * This formatter processes the tokens marked by the preformatter NoCommentsAtLineEnd. That class also registers this
 * class, so no need to add it manually to Settings.
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

        foreach ($job->tree as $token) {
            if (in_array($token, $this->preformatter->tokensToMove(), true)) {
                $statement = $token->statement;

                $statement->removeToken($token);

                $previousStatement = $statement->previous();

                if ($previousStatement === null) {
                    $previousStatement = new Statement($statement->block);

                    $statement->block->insertStatementBefore($previousStatement, $statement);
                }

                $previousStatement->prependToken($token);
            }
        }
    }
}
