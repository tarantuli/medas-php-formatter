<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Formatters\{Prs12\KeywordsToLowercase,
    Prs12\Psr12BlankLines,
    Prs12\Psr12ElseifWhile,
    Prs12\Psr12VisibilityMarkers,
    Prs12\Psr12Whitespace};

class Psr12 extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setMaxLineLength(80);

        $this->addFormatter(service(KeywordsToLowercase::class));
        $this->addFormatter(service(Psr12VisibilityMarkers::class));
        $this->addFormatter(service(Psr12ElseifWhile::class));
        $this->addFormatter(service(Psr12BlankLines::class));
        $this->addFormatter(service(Psr12Whitespace::class));
    }
}
