<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

class ImportSettings
{
    public bool $importGlobalNamespace = false;
    public ?int $maxRelativeDepth = null;
}
