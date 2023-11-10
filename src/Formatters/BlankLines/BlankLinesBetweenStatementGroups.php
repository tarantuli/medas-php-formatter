<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\GenericStatement, TokenGroups};

#[Service]
readonly class BlankLinesBetweenStatementGroups extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
        private TokenGroups         $tokenGroups,
    )
    {
    }

    public function format(Job $job): void
    {
        $previousSubType = null;

        foreach ($job->tree->statements() as $statement) {
            if ($statement->rootStatement !== null) {
                continue;
            }

            if (!$this->typeFinder->for($statement) instanceof GenericStatement) {
                $previousSubType = null;

                continue;
            }

            if (!$statement->previous()) {
                $previousSubType = null;
            }
            elseif ($statement->previous()->rootStatement !== null && $statement->rootStatement === null) {
                $statement->previous()->blankLineAfter();
            }

            $isAssignment = false;

            foreach ($statement as $token) {
                if ($token->is($this->tokenGroups->assignmentOperators())) {
                    $isAssignment = true;

                    break;
                }
            }

            if (
                $previousSubType !== null
                && $isAssignment !== $previousSubType
                && !$statement->firstToken()->inString
            ) {
                $statement->previous()->blankLineAfter();
            }

            $previousSubType = $isAssignment;
        }
    }
}
