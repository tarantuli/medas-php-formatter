<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\Core\Attributes\Service;
use Medas\PhpFormatter\Formatters\Formatter;

#[Service]
readonly class SettingsHandler
{
    /** @return Formatter[] */
    public function formatters(Settings $settings): array
    {
        $formatters = $settings->formatters;

        foreach ($settings->preformatters as $preformatter) {
            foreach ($preformatter->additionalFormatters() as $additionalFormatter) {
                $formatters[] = $additionalFormatter;
            }
        }

        foreach ($formatters as $formatter) {
            foreach ($formatter->additionalFormatters() as $additionalFormatter) {
                $formatters[] = $additionalFormatter;
            }
        }

        // Sort by priority, higher values first
        usort($formatters, fn(Formatter $a, Formatter $b) => -($a->priority() <=> $b->priority()));

        return $formatters;
    }
}
