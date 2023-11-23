<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\Token;

/**
 * Use "type|null" instead of "?type" in property, parameter and return type definitions
 */
#[Service]
readonly class UseUnionNullInsteadOfNullable extends BaseFormatter
{
    public function priority(): int
    {
        return 2200;
    }

    public function format(Job $job): void
    {
        foreach ($job->tokens as $token) {
            if ($token->inTypeDeclaration
                    && $token->next
                    && $token->is(T_QUESTION_MARK)
                    && $token->next->is([T_STRING, T_ARRAY, T_CALLABLE])) {
                $this->replaceByUnionNull($token);
            }
        }
    }

    private function replaceByUnionNull(Token $token): void
    {
        $statement = $token->statement;

        // Pipe token
        $pipeToken = new Token(124, '|');
        $pipeToken->context = $token->context;
        $pipeToken->inTypeDeclaration = true;

        // Null token
        $nullToken = new Token(T_STRING, 'null');
        $nullToken->context = $token->context;
        $nullToken->inTypeDeclaration = true;

        $statement->insertTokenAfter($pipeToken, $token->next);
        $statement->insertTokenAfter($nullToken, $pipeToken);
        $statement->removeToken($token);
    }
}
