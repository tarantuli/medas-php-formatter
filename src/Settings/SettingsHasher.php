<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Settings;

use Medas\Core\Attributes\Service;

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

        $data['formatters'] = [];

        foreach ($settings->formatters() as $formatter) {
            $data['formatters'] = $formatter::class;
        }

        $data['preparsers'] = [];

        foreach ($settings->preparsers() as $preparser) {
            $data['preparsers'] = $preparser::class;
        }

        return sha1(json_encode($data));
    }
}
