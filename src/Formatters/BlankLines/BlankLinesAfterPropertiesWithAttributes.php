<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\ClassPropertyDeclaration};

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
        return 800;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->block() as $statement) {
            // Don't put a blank line after a property that starts with a basic comment;
            // These are most likely group dividers
            if (!$statement->firstToken()->is([T_DOC_COMMENT, T_ATTRIBUTE])) {
                continue;
            }

            if ($this->typeFinder->for($statement) instanceof ClassPropertyDeclaration) {
                $statement->blankLineAfter();
            }
        }
    }
}
