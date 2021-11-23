<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Settings\Indentations\Space;
use Medas\PhpBeautifier\Settings\LineEndings\LineFeed;
use Medas\PhpBeautifier\Formatters\KeywordsToLowercase;
use Medas\PhpBeautifier\Formatters\Psr12BlankLines;
use Medas\PhpBeautifier\Formatters\Psr12ElseifWhile;
use Medas\PhpBeautifier\Formatters\Psr12VisibilityMarkers;
use Medas\PhpBeautifier\Formatters\Psr12Whitespace;

class Psr12 extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setLineEnding(new LineFeed())
            ->setIndentation(new Space(4))
            ->setMaxLineLength(80);

        $this->addFormatter(service(KeywordsToLowercase::class));
        $this->addFormatter(service(Psr12VisibilityMarkers::class));
        $this->addFormatter(service(Psr12ElseifWhile::class));
        $this->addFormatter(service(Psr12BlankLines::class));
        $this->addFormatter(service(Psr12Whitespace::class));
    }
}
