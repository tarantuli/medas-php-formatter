<?php

declare(strict_types=1);

namespace Medas\PhpReformatter;

use Medas\Core\System;
use Medas\FileSystem\TemporaryFiles;
use Medas\PhpReformatter\Exceptions\CannotRunCommandLineException;
use Medas\ServiceManager\Attributes\ConfigValue;
use Medas\ServiceManager\Attributes\Service;

#[Service]
class Validator
{
    private string $errorMessage;

    public function __construct(
        private TemporaryFiles $temporaryFiles,
                               #[ConfigValue('validator.path_to_php')] private string $pathToPhp
    )
    {
    }

    public function validate(string $code): bool
    {
        if (!System::isFunctionAvailable('exec')) {
            throw new CannotRunCommandLineException();
        }

        $tempFile = $this->temporaryFiles->write($code);

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

        $message = str_replace($tempFile, '[code]', $message);
        $lineNumber = $match[1] - 1;
        $codeLines = explode("\n", $code);

        if (array_key_exists($lineNumber, $codeLines)) {
            $message = str_replace('[code]', sprintf('"%s"', $codeLines[$lineNumber]), $message);
        }

        return $message;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
