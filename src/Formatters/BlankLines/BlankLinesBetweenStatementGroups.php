<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Statement, StatementTypeFinder, StatementTypes\GenericStatement, TokenGroups};

#[Service]
readonly class BlankLinesBetweenStatementGroups extends BaseFormatter
{
    private array $constructs;

    public function __construct(
        private StatementTypeFinder $typeFinder,
        private TokenGroups         $tokenGroups,
    )
    {
        $this->constructs = [
            T_BREAK,
            T_CONTINUE,
            T_ECHO,
            T_EXIT,
            T_GLOBAL,
            T_GOTO,
            T_INCLUDE,
            T_INCLUDE_ONCE,
            T_REQUIRE,
            T_REQUIRE_ONCE,
            T_RETURN,
            T_YIELD,
            T_YIELD_FROM,
        ];
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

            $subType = $this->subType($statement);

            if ($previousSubType !== null && $subType !== $previousSubType && !$statement->firstToken()->inString) {
                $statement->previous()->blankLineAfter();
            }

            $previousSubType = $subType;
        }
    }

    private function subType(Statement $statement): int|string
    {
        $foundFirstTrueToken = false;
        $firstTrueTokenText = null;

        foreach ($statement as $token) {
            if ($token->is($this->tokenGroups->assignmentOperators())) {
                return 1;
            }

            if ($token->is($this->constructs)) {
                return 2;
            }

            if ($token->is(T_DOUBLE_ARROW) && $statement->block->opener->containsType(T_MATCH)) {
                return 3;
            }

            if (!$foundFirstTrueToken && !$token->is($this->tokenGroups->comments())) {
                $foundFirstTrueToken = true;
                $firstTrueTokenText = $token->text;
            }
        }

        return $firstTrueTokenText;
    }
}
