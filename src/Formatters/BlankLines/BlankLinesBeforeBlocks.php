<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Helpers, Job};
use Medas\PhpTokenizer\StatementTypes\{
    ClassDeclaration,
    ControlStatement,
    FunctionDeclaration,
    ReturnStatement,
    SwitchBranch,
    ThrowStatement
};

#[Service]
readonly class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(
        private Helpers\BlankLineAdder $blankLineAdder,
    )
    {
    }

    public function priority(): int
    {
        return 300;
    }

    public function format(Job $job): void
    {
        $this->blankLineAdder->beforeTypes($job->tree, [
            ClassDeclaration::class,
            ControlStatement::class,
            FunctionDeclaration::class,
            ReturnStatement::class,
            SwitchBranch::class,
            ThrowStatement::class,
        ]);
    }
}
