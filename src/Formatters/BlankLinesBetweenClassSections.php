<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\StatementTypes\{ClassConstDeclaration, ClassPropertyDeclaration, Comment, UseTraitStatement};

#[Service]
readonly class BlankLinesBetweenClassSections extends BaseFormatter
{
    public function __construct(
        private BlankLineAdder $blankLineAdder,
    )
    {
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
