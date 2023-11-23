<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Formatters\Helpers, Job};
use Medas\PhpTokenizer\StatementTypes\{
    ClassConstDeclaration,
    ClassPropertyDeclaration,
    Comment,
    UseTraitStatement
};

#[Service]
readonly class BlankLinesBetweenClassSections extends BaseFormatter
{
    public function __construct(
        private Helpers\BlankLineAdder $blankLineAdder,
    )
    {
    }

    public function priority(): int
    {
        return 900;
    }

    public function format(Job $job): void
    {
        $this->blankLineAdder->afterTypes($job->tree, [
            UseTraitStatement::class,
            ClassConstDeclaration::class,
            ClassPropertyDeclaration::class,
        ]);

        $this->blankLineAdder->beforeTypes($job->tree, [
            Comment::class,
        ]);
    }
}
