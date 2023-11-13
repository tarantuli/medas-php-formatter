<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{Block, Statement, StatementTypeFinder, StatementTypes\ControlStatement, Token};

#[Service]
readonly class SingleLineControlBodiesEncloser extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->block() as $statement) {
            $isSingleLineControlBody = $this->typeFinder->for($statement) instanceof ControlStatement
                && !$statement->lastToken()->is(T_CURLY_BRACKET_OPEN);

            $isWhileAfterDo = $statement->firstToken()->is(T_WHILE)
                && $statement->lastToken()->previous->is(T_ROUND_BRACKET_CLOSE);

            if ($isSingleLineControlBody && !$isWhileAfterDo) {
                $this->process($statement);
            }
        }
    }

    private function process(Statement $statement): void
    {
        $this->appendClosingStatement($statement);

        $bodyStatement = $this->insertBodyStatement($statement);

        $this->transferTokens($statement, $bodyStatement);
        $this->appendBlockOpener($statement);
    }

    private function appendClosingStatement(Statement $statement): void
    {
        $closingStatement = new Statement($statement->block);

        $closingStatement->appendToken(new Token(1, '}'));

        $statement->block->insertStatementAfter($closingStatement, $statement);
    }

    private function insertBodyStatement(Statement $statement): Statement
    {
        $bodyBlock = new Block($statement->block->depth + 1, $statement);
        $bodyStatement = $bodyBlock->appendNewStatement();

        $statement->block->insertStatementAfter($bodyBlock, $statement);

        return $bodyStatement;
    }

    private function appendBlockOpener(Statement $statement): void
    {
        $statement->appendToken(new Token(1, '{'));
    }

    private function transferTokens(Statement $statement, Statement $bodyStatement): void
    {
        $depth = 0;
        $inBody = false;

        foreach ($statement as $token) {
            if ($inBody) {
                $statement->removeToken($token);

                $bodyStatement->appendToken($token);
            }

            if ($token->is(T_ROUND_BRACKET_OPEN)) {
                ++$depth;
            }

            if ($token->is(T_ROUND_BRACKET_CLOSE)) {
                --$depth;

                if ($depth === 0) {
                    $inBody = true;
                }
            }
        }
    }
}
