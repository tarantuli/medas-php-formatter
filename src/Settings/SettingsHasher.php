<?php

declare(strict_types=1);

namespace Medas\PhpBeautifier\Settings;

use Medas\ServiceManager\Attributes\Service;

#[Service]
class SettingsHasher
{
    public function hash(Settings $settings): string
    {
        $data = [
            (string) $settings->document->lineEnding(),
            (string) $settings->document->indentation(),
            $settings->document->maxLineLength(),
            $settings->import->importGlobalNamespace,
            $settings->import->maxRelativeDepth,
        ];

        foreach ($settings->formatters() as $formatter) {
            $data[] = $formatter::class;
        }

        return sha1(json_encode($data));
    }
}
