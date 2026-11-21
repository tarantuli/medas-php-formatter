<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Statement, Token};

/**
 * There is not a "static analysis agnostic" way to determine which object operators are good points to break on,
 * and which are not. This is the only formatter that looks at the original given code and its whitespace;
 * it keeps the line breaks before object operators (`->` and `?->`) in the original layout.
 */
#[Service]
readonly class KeepOriginalObjectOperatorBreaks extends BaseFormatter
{
    public function priority(): int
    {
        return 1000;
    }

    public function format(Job $job): void
    {
        foreach ($job->tokens as $token) {
            $isObjectOperator = $token->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR]);

            if ($isObjectOperator && $token->previous->originalLineBreaksAfter > 0) {
                $this->splitStatementFrom($token);
            }
        }
    }

    private function splitStatementFrom(Token $token): void
    {
        $statement = $token->statement;
        $rootStatement = $statement->rootStatement ?? $statement;
        $newStatement = new Statement($statement->block);

        $newStatement->rootStatement = $rootStatement;
        $newStatement->additionalDepth = $rootStatement->additionalDepth + 1;

        $statement->block->insertStatementAfter($newStatement, $statement);

        // A blank line after the statement comes after its last part
        $newStatement->blankLineAfter($statement->blankLineAfter);
        $statement->blankLineAfter(false);

        $index = $statement->getIndex($token);

        while ($aToken = $statement->getToken($index)) {
            $statement->removeToken($aToken);

            $newStatement->appendToken($aToken);
        }
    }
}
