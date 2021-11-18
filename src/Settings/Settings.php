<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\BlockFormatters\BlockFormatter;
use Medas\PhpBeautifier\TokenFormatters\RequiredWhitespace;
use Medas\PhpBeautifier\TokenFormatters\TokenFormatter;

class Settings
{
    public DocumentSettings $document;

    /** @var TokenFormatter[] */
    private array $tokenFormatters = [];

    /** @var BlockFormatter[] */
    private array $blockFormatters = [];

    public function __construct()
    {
        $this->document = new DocumentSettings();
        $this->addTokenFormatter(service(RequiredWhitespace::class));
    }

    public function addTokenFormatter(TokenFormatter $formatter): self
    {
        $this->tokenFormatters[] = $formatter;

        return $this;
    }

    public function tokenFormatters(): array
    {
        return $this->tokenFormatters;
    }

    public function addBlockFormatter(BlockFormatter $formatter): self
    {
        $this->blockFormatters[] = $formatter;

        return $this;
    }

    public function blockFormatters(): array
    {
        return $this->blockFormatters;
    }
}
