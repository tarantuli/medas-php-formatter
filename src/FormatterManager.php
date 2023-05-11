<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\AdditionalTokensDefiner;

#[Service]
class FormatterManager
{
    private Settings\Settings $settings;

    public function __construct(
        private readonly AdditionalTokensDefiner $additionalTokensDefiner,
        private readonly Formatter               $formatter)
    {
        $this->additionalTokensDefiner->define();
        $this->settings = new Settings\Medas();
    }

    public function format(string $code, Settings\Settings $settings = null): string
    {
        $settings ??= $this->settings;

        return $this->formatter->format($code, $settings);
    }
}
