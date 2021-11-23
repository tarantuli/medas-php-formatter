<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpBeautifier\Tokens\BlockDumper;
use Medas\PhpBeautifier\Tokens\ContextFinder;
use Medas\PhpBeautifier\Tokens\StructureFinder;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Formatter
{
    private TokenCollection $tokens;
    private Settings\Settings $settings;

    public function __construct(
        private BlockPrinter $blockPrinter,
    )
    {
    }

    /** @noinspection RedundantSuppression */
    public function format(TokenCollection $tokens, Settings\Settings $settings): string
    {
        $this->tokens = $tokens;
        $this->settings = $settings;

        $this->stripWhitespace();
        $this->determineStructure();
        $this->determineContext();
        $this->applyFormatters();

        if (false) {
            /** @noinspection PhpUnreachableStatementInspection */
            service(BlockDumper::class)->dump($this->tokens->structure);
        }

        $result = $this->blockPrinter->print(
            $this->tokens->structure,
            (string) $this->settings->document->indentation(),
            (string) $this->settings->document->lineEnding());

        $this->assertCodeIsValid($result);

        return $result;
    }

    private function stripWhitespace()
    {
        $this->tokens->removeByType(T_WHITESPACE);
    }

    private function determineStructure()
    {
        $structureFinder = service(StructureFinder::class);
        $this->tokens->structure = $structureFinder->determine($this->tokens);
    }

    private function determineContext()
    {
        $contextFinder = service(ContextFinder::class);
        $contextFinder->determine($this->tokens->structure);
    }

    private function applyFormatters(): void
    {
        foreach ($this->settings->formatters() as $formatter) {
            $formatter->format($this->tokens);
        }
    }

    private function assertCodeIsValid(string $code): void
    {
        $validator = service(CodeValidator::class);

        if (!$validator->validate($code)) {
            throw new ReformattedCodeIsInvalidException($code, $validator->getErrorMessage());
        }
    }

}
