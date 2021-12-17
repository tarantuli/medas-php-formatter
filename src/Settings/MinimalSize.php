<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\PhpBeautifier\Settings\Indentations\NoIndentation;
use Medas\PhpBeautifier\Settings\LineEndings\NoLineEnding;

class MinimalSize extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setLineEnding(new NoLineEnding())
            ->setIndentation(new NoIndentation());
    }
}
