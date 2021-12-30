<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\PhpFormatter\Settings\Indentations\NoIndentation;
use Medas\PhpFormatter\Settings\LineEndings\NoLineEnding;

class MinimalSize extends Settings
{
    public function __construct()
    {
        parent::__construct();

        $this->document->setLineEnding(new NoLineEnding())
            ->setIndentation(new NoIndentation());
    }
}
