<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier;

use Medas\PhpBeautifier\Tokens\AdditionalTokensDefiner;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class PhpBeautifier
{
    private Settings\Settings $settings;

    public function __construct(
        private AdditionalTokensDefiner $additionalTokensDefiner,
        private Reformatter             $reformatter)
    {
        $this->settings = new Settings\Psr12();
    }

    public function beautify(string $code, Settings\Settings $settings = null): string
    {
        $settings ??= $this->settings;
        $tokens = new TokenCollection($code);

        return $this->reformatter->reformat($tokens, $settings);
    }
}
