<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\Service;
use Medas\PhpTokenizer\AdditionalTokensDefiner;

#[Service]
readonly class FormatterManager
{
    public function __construct(
        private AdditionalTokensDefiner $additionalTokensDefiner,
        private Formatter $formatter,
    )
    {
        $this->additionalTokensDefiner->define();
    }

    public function format(string $code, Settings\Settings $settings): string
    {
        return $this->formatter->format($code, $settings);
    }
}
