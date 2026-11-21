<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting\ServiceConstructionParameters;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Helpers\ParameterList;
use Medas\PhpTokenizer\{Statement, Token, TokenType};

#[Service]
readonly class ParameterExtractor
{
    public function __construct(
        private ParameterList $parameterList,
    )
    {
    }

    /**
     * The parameters of a method declaration, each with its trailing comma, and the index of the first one.
     *
     * @return array{Token[][], int|null}
     */
    public function extract(Statement $statement): array
    {
        $open = $this->parameterList->opener($statement);

        if ($open === null || $open->next === $open->matchingBracket) {
            return [[], null];
        }

        $parameters = [];
        $parameter = [];

        for ($token = $open->next; $token !== $open->matchingBracket; $token = $token->next) {
            $parameter[] = $token;

            // A comma between parameters, not one in a default value like [1, 2]
            if ($token->is(TokenType::COMMA) && $token->bracketDepth === $open->bracketDepth + 1) {
                $parameters[] = $parameter;
                $parameter = [];
            }
        }

        if ($parameter) {
            $parameters[] = $parameter;
        }

        return [$parameters, $statement->getIndex($open->next)];
    }
}
