<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Tokens;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class Tokenizer
{
    public function __construct(private readonly AdditionalTokensDefiner $additionalTokensDefiner,
                                private readonly StructureFinder         $structureFinder,
                                private readonly ContextAdder            $contextAdder)
    {
        // AdditionalTokensDefiner only needs to be initialized, it isn't used otherwise
    }

    public function makeTree(string $code): TokenTree
    {
        $collection = $this->tokenize($code);
        return $this->determineTree($collection);
    }

    public function tokenize(string $code): TokenCollection
    {
        $collection = new TokenCollection();

        $this->addTokens($collection, $code);
        $collection->setSourceHash(sha1($code));

        return $collection;
    }

    private function addTokens(TokenCollection $collection, string $code): void
    {
        $tokens = Token::tokenize($code, TOKEN_PARSE);

        foreach ($tokens as $token) {
            if ($token->is(T_OPEN_TAG)) {
                $token->text = rtrim($token->text);
            }

            $collection->add($token);
        }
    }

    public function determineTree(TokenCollection $tokens): TokenTree
    {
        $tree = $this->determineStructure($tokens);
        $this->addContext($tree);

        return $tree;
    }

    private function determineStructure(TokenCollection $tokens): TokenTree
    {
        return $this->structureFinder->determine($tokens);
    }

    private function addContext(TokenTree $tree): void
    {
        $this->contextAdder->add($tree);
    }
}
