<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\BlockFormatters\Psr12BlankLines;
use Medas\PhpBeautifier\BlockFormatters\Psr12ElseifWhile;
use Medas\PhpBeautifier\Settings\Indentations\Space;
use Medas\PhpBeautifier\Settings\LineEndings\LineFeed;
use Medas\PhpBeautifier\TokenFormatters\KeywordsToLowercase;
use Medas\PhpBeautifier\TokenFormatters\Psr12Whitespace;

class Psr12 extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setLineEnding(new LineFeed())
            ->setIndentation(new Space(4))
            ->setMaxLineLength(80);

        $this->addBlockFormatter(service(Psr12ElseifWhile::class));
        $this->addBlockFormatter(service(Psr12BlankLines::class));

        $this->addTokenFormatter(service(KeywordsToLowercase::class));
        $this->addTokenFormatter(service(Psr12Whitespace::class));
    }
}
