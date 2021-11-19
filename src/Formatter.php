<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Exceptions\ReformattedCodeIsInvalidException;
use Medas\PhpBeautifier\Tokens\Block;
use Medas\PhpBeautifier\Tokens\BlockDumper;
use Medas\PhpBeautifier\Tokens\StructureFinder;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Formatter
{
    private TokenCollection $tokens;
    private Block $document;
    private Settings\Settings $settings;

    public function __construct(
        private BlockPrinter $blockPrinter,
    )
    {
    }

    public function format(TokenCollection $tokens, Settings\Settings $settings): string
    {
        $this->tokens = $tokens;
        $this->settings = $settings;

        $this->stripWhitespace();
        $this->determineStructure();
        //$this->applyFormatters();

        if (true) {
            /** @noinspection PhpUnreachableStatementInspection */
            service(BlockDumper::class)->dump($this->document);
        }

        $result = $this->blockPrinter->print(
            $this->document,
            (string) $this->settings->document->indentation(),
            (string) $this->settings->document->lineEnding());

        if (true) {
            echo $result;
        }

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
        $this->document = $structureFinder->determine($this->tokens);
    }

    private function applyFormatters(): void
    {
        foreach ($this->settings->blockFormatters() as $formatter) {
            $formatter->format($this->document);
        }

        foreach ($this->settings->tokenFormatters() as $formatter) {
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
