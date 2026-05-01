<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\SwitchBranch};

#[Service]
readonly class SwitchIndentationAdder extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 460;
    }

    public function format(Job $job): void
    {
        $tree = $job->tree;
        $switchDepths = [];

        foreach ($tree->statements() as $statement) {
            $type = $this->typeFinder->for($statement);

            if ($statement->firstToken()->is(T_CURLY_BRACKET_CLOSE)) {
                $switchDepths = array_filter(
                    $switchDepths,
                    fn($depth) => $depth !== $statement->block->depth + 1
                );
            }

            // Add additional depth levels to each statement equal to the number of open switch blocks
            // decreased by one if this statement itself is a case or default statement
            $statement->additionalDepth += count($switchDepths)
                - (int) ($type instanceof SwitchBranch);

            $firstNonCommentToken = $statement->firstNonCommentToken();

            if ($firstNonCommentToken && $firstNonCommentToken->is(T_SWITCH)) {
                $switchDepths[] = $statement->block->depth + 1;
            }
        }
    }
}
