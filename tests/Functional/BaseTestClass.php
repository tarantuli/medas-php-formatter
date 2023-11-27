<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\Console\Diff;
use Medas\ConsolePrinter\ConsolePrinter;
use Medas\PhpFormatter\{FormatterManager, Settings\Medas, Settings\Settings};
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Diff\{Differ, Output\UnifiedDiffOutputBuilder};

abstract class BaseTestClass extends TestCase
{
    protected function assertRemainsTheSame(string $sourceFile, Settings $settings = null): void
    {
        if ($settings === null) {
            $settings = new Medas();
        }

        $this->compare($sourceFile, $sourceFile, $settings, "$sourceFile changed after formatting");
    }

    protected function compare(string $sourceFile, string $expectedFile, Settings $settings, string $message): void
    {
        [$result, $expected] = $this->format($sourceFile, $expectedFile, $settings);

        if ($result !== $expected) {
            // Write the diff to output to ease development
            $diff = (new Differ(new UnifiedDiffOutputBuilder("--- Expected\n+++ Actual\n")))->diff(
                $expected,
                $result
            );

            service(ConsolePrinter::class)->print(new Diff($diff));
        }

        self::assertEquals($expected, $result, $message);
    }

    protected function format(string $sourceFile, string $expectedFile, Settings $settings): array
    {
        $formatterManager = service(FormatterManager::class);

        $source = str_replace(
            "\r\n",
            "\n",
            file_get_contents(__DIR__ . '/../TestFiles/' . $sourceFile . '.php')
        );

        $expected = str_replace(
            "\r\n",
            "\n",
            file_get_contents(__DIR__ . '/../TestFiles/' . $expectedFile . '.php')
        );

        return [$formatterManager->format($source, $settings), $expected];
    }

    protected function assertChanges(string $sourceFile, string $targetFile, Settings $settings = null): void
    {
        if ($settings === null) {
            $settings = new Medas();
        }

        $this->compare($sourceFile, $targetFile, $settings, "$sourceFile => $targetFile");
    }
}
