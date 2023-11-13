<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

class MinimalSize extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setLineEnding(new LineEndings\NoLineEnding())
            ->setIndentation(new Indentations\NoIndentation());
    }
}
