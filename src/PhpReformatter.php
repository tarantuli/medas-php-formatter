<?php

declare(strict_types=1);

namespace Medas\PhpReformatter;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class PhpReformatter
{
    private Settings\Settings $settings;

    public function __construct()
    {
        $this->settings = new Settings\Settings();
    }

    public function reformat(string $code, Settings\Settings $settings = null): string
    {
        $settings ??= $this->settings;
        $tokens = \PhpToken::tokenize($code, TOKEN_PARSE);

        $reformatter = new Reformatter($tokens, $settings);

        return $reformatter->reformat();
    }
}
