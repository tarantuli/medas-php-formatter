<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\LineSplitting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Helpers\ParameterList;
use Medas\PhpTokenizer\{Statement, TokenType};

/**
 * Splits a function declaration that is too long into one parameter per line.
 */
#[Service]
readonly class FunctionDeclarationSplitter
{
    public function __construct(
        private Helpers\StatementSplitter $splitter,
        private ParameterList             $parameterList,
    )
    {
    }

    public function split(Statement $statement): bool
    {
        $opener = $this->parameterList->opener($statement);
        $closer = $opener?->matchingBracket;

        if ($closer === null) {
            return false;
        }

        $this->splitter->split(
            $statement,
            [TokenType::COMMA],
            true,
            $statement->getIndex($opener),
            $statement->getIndex($closer),
            1
        );

        return true;
    }
}
