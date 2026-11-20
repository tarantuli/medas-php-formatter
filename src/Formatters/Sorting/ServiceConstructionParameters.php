<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{DeclarationKind, Statement, Token, TokenType};

#[Service]
readonly class ServiceConstructionParameters extends BaseFormatter
{
    public function __construct(
        private ServiceConstructionParameters\IsServiceClassFinder $isServiceClassFinder,
        private ServiceConstructionParameters\ParameterComparer    $parameterComparer,
        private ServiceConstructionParameters\ParameterExtractor   $parameterExtractor,
    )
    {
    }

    public function priority(): int
    {
        return 1450;
    }

    public function format(Job $job): void
    {
        $inServiceClass = false;

        foreach ($job->tree->statements() as $statement) {
            $declaration = $statement->declaration;

            if ($declaration?->kind->isClassLike()) {
                $inServiceClass = $this->isServiceClassFinder->isServiceClass($statement);
            }

            if ($inServiceClass
                    && $declaration?->kind === DeclarationKind::Method
                    && strtolower($declaration->name) === '__construct') {
                $this->sortParameters($statement);
            }
        }
    }

    private function sortParameters(Statement $statement): void
    {
        [$parameters, $firstIndex] = $this->parameterExtractor->extract($statement);

        if (count($parameters) < 2) {
            return;
        }

        usort($parameters, $this->parameterComparer->compare(...));

        $parameters = $this->repairCommas($parameters);
        $previousToken = $statement->getToken($firstIndex - 1);

        foreach ($parameters as $parameter) {
            foreach ($parameter as $token) {
                if ($statement->getIndex($token) !== $statement->getIndex($previousToken) + 1) {
                    $statement->moveTokenAfter($token, $previousToken);
                }

                $previousToken = $token;
            }
        }
    }

    /**
     * Without a trailing comma, the last parameter has no comma of its own. If sorting moved it away from the end, it
     * takes the comma of the parameter that's now last.
     *
     * @param Token[][] $parameters
     * @return Token[][]
     */
    private function repairCommas(array $parameters): array
    {
        $lastIndex = array_key_last($parameters);

        foreach ($parameters as $index => $parameter) {
            if ($index === $lastIndex || end($parameter)->is(TokenType::COMMA)) {
                continue;
            }

            if (end($parameters[$lastIndex])->is(TokenType::COMMA)) {
                $parameters[$index][] = array_pop($parameters[$lastIndex]);
            }
        }

        return $parameters;
    }
}
