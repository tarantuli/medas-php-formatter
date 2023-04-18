<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\Attributes\ConfigValue;
use Medas\Core\Attributes\Service;
use Medas\Core\System;
use Medas\FileSystem\TemporaryFiles;
use Medas\PhpFormatter\ConfigOptions\PathToPhp;
use Medas\PhpFormatter\Exceptions\CannotRunCommandLineException;

#[Service]
class CodeValidator
{
    private string $errorMessage;

    public function __construct(
        private readonly TemporaryFiles                          $temporaryFiles,
        #[ConfigValue(PathToPhp::class)] private readonly string $pathToPhp
    )
    {
    }

    public function validate(string $code): bool
    {
        if (!System::isFunctionAvailable('exec')) {
            throw new CannotRunCommandLineException();
        }

        $tempFile = $this->temporaryFiles->create($code);

        $command = sprintf(
            '%s -l -n -d display_errors=1 %s',
            escapeshellarg($this->pathToPhp),
            escapeshellarg($tempFile)
        );

        exec($command, $results);

        $results = implode("\n", array_filter($results));

        if (str_contains($results, 'No syntax errors detected in')) {
            return true;
        }

        $this->errorMessage = $this->replaceFilenameBySourceLine($results, $tempFile, $code);

        return false;
    }

    private function replaceFilenameBySourceLine(string $message, string $tempFile, string $code): string
    {
        if (!preg_match('/line (\d+)/', $message, $match)) {
            return $message;
        }

        $lineNumber = $match[1] - 1;
        $codeLines = explode("\n", $code);

        if (array_key_exists($lineNumber, $codeLines)) {
            $message = str_replace($tempFile, sprintf('"%s"', $codeLines[$lineNumber]), $message);
        }

        return $message;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
