<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters\Psr12;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\{BaseFormatter, Helpers\ReturnTypeTokens};
use Medas\PhpFormatter\Job;
use Medas\PhpTokenizer\{
    Contexts\MethodDeclaration,
    Statement,
    StatementTypeFinder,
    StatementTypes\FunctionDeclaration
};

#[Service]
readonly class PromotedPropertyHookSplitter extends BaseFormatter
{
    public function __construct(
        private ReturnTypeTokens    $returnTypeTokens,
        private StatementTypeFinder $typeFinder,
    )
    {
    }

    public function priority(): int
    {
        // Must run before CurlyBlockDepthPropagator (480) so the propagator sees
        // the correct additionalDepth on the split statements.
        return 490;
    }

    public function format(Job $job): void
    {
        // Collect first, then split — avoids concurrent modification of the generator.
        $constructors = [];

        foreach ($job->tree->statements() as $statement) {
            if ($this->isPromotedHookConstructor($statement)) {
                $constructors[] = $statement;
            }
        }

        foreach ($constructors as $constructor) {
            $this->split($job, $constructor);
        }
    }

    private function isPromotedHookConstructor(Statement $statement): bool
    {
        $context = $statement->firstToken()?->context ?? null;

        if (!$context instanceof MethodDeclaration) {
            return false;
        }

        if (!$this->typeFinder->for($statement) instanceof FunctionDeclaration) {
            return false;
        }

        if (!$statement->lastToken()?->is(T_CURLY_BRACKET_OPEN)) {
            return false;
        }

        // isReturnTypeToken returns true for normal method declarations where { is preceded
        // by ) through return-type tokens. A promoted hook constructor has { preceded by a
        // variable or default value, so isReturnTypeToken returns false.
        return !$this->returnTypeTokens->isReturnTypeToken($statement->lastToken());
    }

    private function split(Job $job, Statement $constructorStatement): void
    {
        $constructorBlock = $constructorStatement->block;

        // Collect all related statements BEFORE modifying the tree, since modification
        // invalidates ongoing generators.
        $classLevelStatements = [];
        $collecting = false;

        foreach ($job->tree->statements() as $statement) {
            if ($statement === $constructorStatement) {
                $collecting = true;

                continue;
            }

            if (!$collecting) {
                continue;
            }

            if ($statement->block === $constructorBlock) {
                $classLevelStatements[] = $statement;

                if ($this->isConstructorBodyOpener($statement)) {
                    break;
                }
            }
        }

        // Split the constructor declaration after its opening (.
        // "public function __construct( public string $name {"
        // becomes:
        //   S0  "public function __construct("          additionalDepth=0
        //   S0a "public string $name {"                 additionalDepth=1
        $this->splitAfterOpenParen($constructorStatement);

        // Process each class-body-level statement that belongs to this constructor.
        foreach ($classLevelStatements as $statement) {
            if (!$statement->firstToken()?->is(T_CURLY_BRACKET_CLOSE)) {
                continue;
            }

            if ($this->isConstructorBodyOpener($statement)) {
                $this->processConstructorBodyOpener($statement);
            }
            else {
                $this->splitGroupStatement($statement, $constructorStatement);
            }
        }
    }

    /**
     * Splits the constructor declaration at its first (.
     * Moves all tokens after ( to a new statement at additionalDepth=1.
     */
    private function splitAfterOpenParen(Statement $constructorStatement): void
    {
        $parenIndex = null;

        foreach ($constructorStatement as $i => $token) {
            if ($token->is(T_ROUND_BRACKET_OPEN)) {
                $parenIndex = $i;

                break;
            }
        }

        if ($parenIndex === null) {
            return;
        }

        $firstParamStatement = new Statement($constructorStatement->block);

        $firstParamStatement->rootStatement = $constructorStatement;
        $firstParamStatement->additionalDepth = 1;

        $constructorStatement->block->insertStatementAfter(
            $firstParamStatement,
            $constructorStatement
        );

        // Mark as FunctionDeclaration so AlignArgumentNames aligns all hook opener
        // statements together under the same rootStatement.
        $firstParamStatement->setType(FunctionDeclaration::instance());

        $totalTokens = $constructorStatement->tokenCount();

        for ($i = $totalTokens - 1; $i > $parenIndex; $i--) {
            $token = $constructorStatement->getToken($i);

            $constructorStatement->removeToken($token);

            $firstParamStatement->prependToken($token);
        }

        // Add space before { in the first promoted hook opener.
        if ($firstParamStatement->lastToken()?->is(T_CURLY_BRACKET_OPEN)) {
            $firstParamStatement->getToken(-2)?->spaceAfter();
        }
    }

    private function isConstructorBodyOpener(Statement $statement): bool
    {
        return $statement->containsType(T_ROUND_BRACKET_CLOSE) && $statement->lastToken()?->is(T_CURLY_BRACKET_OPEN);
    }

    /**
     * Processes the final "}, ) {" statement:
     *   S_a  "},"      additionalDepth=1
     *   S_b  ") {"     additionalDepth=0, with lineBreakAfter on )
     */
    private function processConstructorBodyOpener(Statement $statement): void
    {
        // Find the comma after the closing }.
        $commaIndex = null;

        foreach ($statement as $i => $token) {
            if ($i === 0) {
                continue;
            }

            if ($token->is(T_COMMA)) {
                $commaIndex = $i;

                break;
            }
        }

        if ($commaIndex === null) {
            // No comma — just ") {", set depth and linebreak.
            $this->addLineBreakBeforeOpenBrace($statement);

            return;
        }

        // Create the ") {" statement.
        $bodyOpenerStatement = new Statement($statement->block);

        $bodyOpenerStatement->rootStatement = $statement;
        $bodyOpenerStatement->additionalDepth = 0;

        $statement->block->insertStatementAfter($bodyOpenerStatement, $statement);

        $totalTokens = $statement->tokenCount();

        for ($i = $totalTokens - 1; $i > $commaIndex; $i--) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            $bodyOpenerStatement->prependToken($token);
        }

        $statement->additionalDepth = 0;

        $this->addLineBreakBeforeOpenBrace($bodyOpenerStatement);
    }

    private function addLineBreakBeforeOpenBrace(Statement $statement): void
    {
        $closingParen = $statement->findToken(T_ROUND_BRACKET_CLOSE);

        $closingParen?->lineBreakAfter();
    }

    /**
     * Splits a "}, public Type $prop = default {" statement into two:
     *   S_a  "},"                                   additionalDepth=1
     *   S_b  "public Type $prop = default {"        additionalDepth=1 (space before {)
     */
    private function splitGroupStatement(Statement $statement, Statement $constructorStatement): void
    {
        // Find the comma that separates the hook closer from the next parameter.
        $commaIndex = null;

        foreach ($statement as $i => $token) {
            if ($i === 0) {
                // Skip the leading }
                continue;
            }

            if ($token->is(T_COMMA)) {
                $commaIndex = $i;

                break;
            }
        }

        if ($commaIndex === null) {
            return;
        }

        // Create the next hook opener statement.
        $nextStatement = new Statement($statement->block);

        $nextStatement->rootStatement = $constructorStatement;
        $nextStatement->additionalDepth = 1;

        $statement->block->insertStatementAfter($nextStatement, $statement);

        // Mark as FunctionDeclaration so AlignArgumentNames aligns this with sibling
        // hook opener statements that share the same rootStatement.
        $nextStatement->setType(FunctionDeclaration::instance());

        // Move all tokens after the comma into the next statement.
        $totalTokens = $statement->tokenCount();

        for ($i = $totalTokens - 1; $i > $commaIndex; $i--) {
            $token = $statement->getToken($i);

            $statement->removeToken($token);

            $nextStatement->prependToken($token);
        }

        // The original statement is now "}," — start at additionalDepth=0 so that
        // CurlyBlockDepthPropagator adds the correct +1 from the hook body propagation.
        $statement->additionalDepth = 0;

        if ($nextStatement->lastToken()?->is(T_CURLY_BRACKET_OPEN)) {
            $nextStatement->getToken(-2)?->spaceAfter();
        }
    }
}
