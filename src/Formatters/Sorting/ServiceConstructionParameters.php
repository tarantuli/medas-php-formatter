<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Sorting;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Statement,
    StatementTypeFinder,
    StatementTypes\ClassDeclaration,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class ServiceConstructionParameters extends BaseFormatter
{
    public function __construct(
        private ServiceConstructionParameters\ParameterComparer    $parameterComparer,
        private ServiceConstructionParameters\ParameterExtractor   $parameterExtractor,
        private ServiceConstructionParameters\isServiceClassFinder $isServiceClassFinder,
        private StatementTypeFinder                                $typeFinder,
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
            $statementType = $this->typeFinder->for($statement);

            if ($statementType instanceof ClassDeclaration) {
                $inServiceClass = $this->isServiceClassFinder->isServiceClass($statement);
            }

            if ($statementType instanceof FunctionDeclaration && $inServiceClass) {
                if ($this->getFunctionName($statement) === '__construct') {
                    $this->sortParameters($statement);
                }
            }
        }
    }

    private function getFunctionName(Statement $statement): string|null
    {
        foreach ($statement as $token) {
            if ($token->is(T_FUNCTION)) {
                return $statement->getTokenAfter($token)->text;
            }
        }

        return null;
    }

    private function sortParameters(Statement $statement): void
    {
        [$parameters, $firstIndex] = $this->parameterExtractor->extract($statement);

        usort($parameters, $this->parameterComparer->compare(...));

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
}
