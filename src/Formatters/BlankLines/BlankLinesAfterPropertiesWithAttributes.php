<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\BaseFormatter;
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\StatementTypeFinder;
use Medas\PhpTokenizer\StatementTypes\ClassPropertyDeclaration;

#[Service]
readonly class BlankLinesAfterPropertiesWithAttributes extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 200;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->block() as $statement) {
            if (!$statement->firstToken()->is([T_COMMENT, T_DOC_COMMENT, T_ATTRIBUTE])) {
                continue;
            }

            if ($this->typeFinder->for($statement) instanceof ClassPropertyDeclaration) {
                $statement->blankLineAfter();
            }
        }
    }
}
