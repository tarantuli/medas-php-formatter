<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Replacements;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\{Formatters\BaseFormatter, Job};
use Medas\PhpTokenizer\{
    Block,
    BlockType,
    Statement,
    StatementTypeFinder,
    StatementTypes\ControlStatement,
    Token,
    TokenType
};

#[Service]
readonly class SingleLineControlBodiesEncloser extends BaseFormatter
{
    public function __construct(
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        return 2100;
    }

    public function format(Job $job): void
    {
        foreach ($job->tree->block() as $statement) {
            if (!$this->typeFinder->for($statement) instanceof ControlStatement) {
                continue;
            }

            if ($statement->lastToken()->is(TokenType::CURLY_BRACKET_OPEN)) {
                continue;
            }

            if (null !== $bodyStart = $this->findBodyStart($statement)) {
                $this->process($statement, $bodyStart);
            }
        }
    }

    /**
     * Finds the index of the first token of the single-line body.
     *
     * @return int|null `null` when there is no body to enclose: the `while (…);` that ends a do-while loop, an empty
     *                  loop body, or the alternative syntax with `:`
     */
    private function findBodyStart(Statement $statement): int|null
    {
        $tokens = iterator_to_array($statement, false);
        $index = 0;

        while (isset($tokens[$index]) && $tokens[$index]->is([T_COMMENT, T_DOC_COMMENT])) {
            ++$index;
        }

        // "else" and "do" have no condition, unlike "else if"
        if ($tokens[$index]->is(T_DO) || ($tokens[$index]->is(T_ELSE) && !($tokens[$index + 1] ?? null)?->is(T_IF))) {
            $bodyStart = $index + 1;
        }
        else {
            $bodyStart = $this->findIndexAfterCondition($tokens, $index);
        }

        if ($bodyStart === null || !isset($tokens[$bodyStart])) {
            return null;
        }

        if ($tokens[$bodyStart]->is([TokenType::SEMICOLON, TokenType::COLON])) {
            return null;
        }

        return $bodyStart;
    }

    /** @param Token[] $tokens */
    private function findIndexAfterCondition(array $tokens, int $index): int|null
    {
        $depth = 0;

        for (; isset($tokens[$index]); ++$index) {
            if ($tokens[$index]->is(TokenType::ROUND_BRACKET_OPEN)) {
                ++$depth;
            }
            elseif ($tokens[$index]->is(TokenType::ROUND_BRACKET_CLOSE) && --$depth === 0) {
                return $index + 1;
            }
        }

        return null;
    }

    private function process(Statement $statement, int $bodyStart): void
    {
        [$opener, $closer] = $this->makeBraces($statement);

        $this->appendClosingStatement($statement, $closer);

        $bodyStatement = $this->insertBodyStatement($statement);

        $this->transferTokens($statement, $bodyStatement, $bodyStart);

        $statement->appendToken($opener);
    }

    /**
     * The braces get what the tokenizer would have given them, so the formatters after this one see a block.
     *
     * @return Token[]
     */
    private function makeBraces(Statement $statement): array
    {
        $opener = Token::fromText('{', $statement->lastToken());
        $closer = Token::fromText('}', $statement->lastToken());

        $opener->opensBlock = BlockType::Control;
        $opener->matchingBracket = $closer;
        $opener->bracketDepth = $statement->firstToken()->bracketDepth;
        $closer->closesBlock = true;
        $closer->matchingBracket = $opener;
        $closer->bracketDepth = $opener->bracketDepth;

        return [$opener, $closer];
    }

    private function appendClosingStatement(Statement $statement, Token $closer): void
    {
        $closingStatement = new Statement($statement->block);

        $statement->block->insertStatementAfter($closingStatement, $statement);

        $closingStatement->appendToken($closer);
    }

    private function insertBodyStatement(Statement $statement): Statement
    {
        $bodyBlock = new Block($statement->block->depth + 1, $statement, BlockType::Control);
        $bodyStatement = $bodyBlock->appendNewStatement();

        $statement->block->insertStatementAfter($bodyBlock, $statement);

        return $bodyStatement;
    }

    private function transferTokens(Statement $statement, Statement $bodyStatement, int $bodyStart): void
    {
        foreach (array_slice(iterator_to_array($statement, false), $bodyStart) as $token) {
            $statement->removeToken($token);

            $bodyStatement->appendToken($token);
        }
    }
}
