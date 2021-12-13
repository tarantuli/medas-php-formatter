<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Tokens;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class Tokenizer
{
    public function __construct(private AdditionalTokensDefiner $additionalTokensDefiner,
                                private StructureFinder         $structureFinder,
                                private ContextFinder           $contextFinder)
    {
        // AdditionalTokensDefiner only needs to be initialized, it isn't used otherwise
    }

    public function tokenize(string $code, bool $determineStructureAndContext = true): TokenCollection
    {
        $collection = new TokenCollection();

        $this->addTokens($collection, $code);
        $collection->setSourceHash(sha1($code));

        if ($determineStructureAndContext) {
            $this->determineStructureAndContext($collection);
        }

        return $collection;
    }

    private function addTokens(TokenCollection $collection, string $code): void
    {
        $tokens = Token::tokenize($code, TOKEN_PARSE);
        $previousToken = null;

        foreach ($tokens as $token) {
            if ($token->is(T_OPEN_TAG)) {
                $token->text = rtrim($token->text);
            }

            if ($previousToken) {
                $token->previous = $previousToken;
                $previousToken->next = $token;
            }

            $collection->add($token);
            $previousToken = $token;
        }
    }

    public function determineStructureAndContext(TokenCollection $tokens): void
    {
        $this->stripWhitespace($tokens);
        $this->determineStructure($tokens);
        $this->determineContext($tokens);
    }

    private function stripWhitespace(TokenCollection $tokens)
    {
        $tokens->removeByType(T_WHITESPACE);
    }

    private function determineStructure(TokenCollection $tokens)
    {
        $this->structureFinder->determine($tokens);
    }

    private function determineContext(TokenCollection $tokens)
    {
        $this->contextFinder->determine($tokens->structure);
    }
}
