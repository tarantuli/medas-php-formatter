<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\BlankLines;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\ClassPropertyDeclaration, TokenGroups};

#[Service]
readonly class BlankLinesAfterPropertiesWithAttributes extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
        private TokenGroups         $tokenGroups,
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
            if (!$statement->firstToken()->is($this->tokenGroups->comments())) {
                continue;
            }

            if ($this->typeFinder->for($statement) instanceof ClassPropertyDeclaration) {
                $statement->blankLineAfter();
            }
        }
    }
}
