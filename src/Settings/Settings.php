<?php

declare(strict_types=1);

namespace Medas\PhpReformatter\Settings;

class Settings
{
    public DocumentSettings $document;

    public function __construct()
    {
        $this->document = new DocumentSettings();
    }
}
