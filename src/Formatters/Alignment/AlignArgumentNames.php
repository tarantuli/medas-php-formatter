<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Alignment;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{StatementTypeFinder, StatementTypes\FunctionDeclaration, Token};

#[Service]
readonly class AlignArgumentNames extends BaseFormatter
{
    public const VARIABLE_STARTERS = [T_AMPERSAND, T_ELLIPSIS, T_VARIABLE];

    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(Job $job): void
    {
        $prefixLengthsPerRootStatement = $this->determinePrefixLengthsPerRootStatement($job);

        foreach ($job->tree->block() as $statement) {
            if ($statement->rootStatement === null) {
                continue;
            }

            if (!$this->typeFinder->for($statement) instanceof FunctionDeclaration) {
                continue;
            }

            $id = spl_object_id($statement->rootStatement);
            $currentLength = 0;

            foreach ($statement as $token) {
                if (!$token->is(self::VARIABLE_STARTERS)) {
                    if (!$token->inAttribute && !$token->is(T_ATTRIBUTE)) {
                        $currentLength += strlen($token->text) + $token->spaceAfter;
                    }

                    continue;
                }

                if ($token->previous) {
                    $token->previous->extraSpacesAfter += $prefixLengthsPerRootStatement[$id] - $currentLength;
                }
                else {
                    $placeholder = new Token(1, str_repeat(' ', $prefixLengthsPerRootStatement[$id]));

                    $token->statement->prependToken($placeholder);
                }

                break;
            }
        }
    }

    private function determinePrefixLengthsPerRootStatement(Job $job): array
    {
        $prefixLengthsPerRootStatement = [];

        foreach ($job->tree->block() as $statement) {
            if ($statement->rootStatement === null) {
                continue;
            }

            if (!$this->typeFinder->for($statement) instanceof FunctionDeclaration) {
                continue;
            }

            $prefixLength = 0;
            $foundVariable = false;

            foreach ($statement as $token) {
                if ($token->is(self::VARIABLE_STARTERS)) {
                    $foundVariable = true;

                    break;
                }

                if ($token->inAttribute || $token->is(T_ATTRIBUTE)) {
                    continue;
                }

                $prefixLength += strlen($token->text) + $token->spaceAfter;
            }

            if (!$foundVariable) {
                continue;
            }

            $id = spl_object_id($statement->rootStatement);

            $prefixLengthsPerRootStatement[$id] = array_key_exists($id, $prefixLengthsPerRootStatement)
                ? max($prefixLength, $prefixLengthsPerRootStatement[$id])
                : $prefixLength;
        }

        return $prefixLengthsPerRootStatement;
    }
}
