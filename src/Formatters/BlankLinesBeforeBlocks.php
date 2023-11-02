<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\StatementTypes\{ControlStatement, ReturnStatement, SwitchBranch, ThrowStatement};

#[Service]
readonly class BlankLinesBeforeBlocks extends BaseFormatter
{
    public function __construct(
        private Helpers\BlankLineAdder $blankLineAdder,
    )
    {
    }

    public function format(Job $job): void
    {
        $this->blankLineAdder->beforeTypes($job->tree, [
            ControlStatement::class,
            SwitchBranch::class,
            ReturnStatement::class,
            ThrowStatement::class,
        ]);
    }
}
