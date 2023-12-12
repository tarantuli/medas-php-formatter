<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};

#[Service]
readonly class BlankLineBetweenObjectCreationAndManipulation extends BaseFormatter
{
    public function priority(): int
    {
        return 150;
    }

    public function format(Job $job): void
    {
        $previousStatementIsCreation = false;
        $previousVariable = null;

        foreach ($job->tree->statements() as $statement) {
            if ($statement->rootStatement !== null) {
                continue;
            }

            $firstToken = $statement->firstNonCommentToken();

            if (!$firstToken) {
                continue;
            }

            $secondToken = $statement->getToken($statement->getIndex($firstToken) + 1);

            if ($previousStatementIsCreation
                    && $firstToken->is(T_VARIABLE)
                    && $firstToken->text === $previousVariable
                    && $secondToken
                    && $secondToken->is(T_OBJECT_OPERATOR)) {
                // The previous statement was the creation of an object
                // and this one assigns property values or calls methods on that object.
                // Place a blank line between them.
                //
                // Example:
                //      $chart = new Chart();
                //
                //      $chart->doSomething();
                $statement->previous()->blankLineAfter();
            }

            if ($firstToken->is(T_VARIABLE)) {
                $previousStatementIsCreation = !$secondToken
                    || !$secondToken->is(T_OBJECT_OPERATOR);

                $previousVariable = $firstToken->text;
            }
            else {
                $previousStatementIsCreation = false;
            }
        }
    }
}
