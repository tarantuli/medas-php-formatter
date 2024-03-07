<?php

declare(strict_types=1);

namespace Medas\PhpFormatter;

use Medas\Core\{Attributes\ConfigValue, Attributes\Service, System};
use Medas\FileSystem\TemporaryFiles;

#[Service]
readonly class CodeValidator
{
    public function __construct(
        private TemporaryFiles $temporaryFiles,

        #[ConfigValue(ConfigOptions\PathToPhp::class)]
        private string|null    $pathToPhp,
    )
    {
    }

    public function validate(string $code): CodeValidator\ValidatorResult
    {
        if (!System::isFunctionAvailable('exec')) {
            throw new Exceptions\CannotRunCommandLine();
        }

        if ($this->pathToPhp === null) {
            throw new Exceptions\PathToPhpIsNotSet();
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
            return new CodeValidator\ValidatorResult(true);
        }

        return new CodeValidator\ValidatorResult(
            false,
            $this->replaceFilenameBySourceLine($results, $tempFile, $code)
        );
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
}
