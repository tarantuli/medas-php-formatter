<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\BlockFormatters\BlockFormatter;
use Medas\PhpBeautifier\TokenFormatters\RequiredWhitespace;
use Medas\PhpBeautifier\TokenFormatters\TokenFormatter;

class Settings
{
    public DocumentSettings $document;

    /** @var TokenFormatter[]|BlockFormatter[] */
    private array $formatters = [];

    public function __construct()
    {
        $this->document = new DocumentSettings();
        $this->addTokenFormatter(service(RequiredWhitespace::class));
    }

    public function addTokenFormatter(TokenFormatter $formatter): self
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    public function addBlockFormatter(BlockFormatter $formatter): self
    {
        $this->formatters[] = $formatter;

        return $this;
    }

    public function formatters(): array
    {
        return $this->formatters;
    }
}
