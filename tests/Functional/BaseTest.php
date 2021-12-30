<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpFormatter\FormatterManager;
use Medas\PhpFormatter\Settings\Settings;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    protected function assertRemainsTheSame(string $sourceFile, Settings $settings)
    {
        $this->compare($sourceFile, $sourceFile, $settings, "$sourceFile changed after formatting");
    }

    protected function compare(string $sourceFile, string $expectedFile, Settings $settings, string $message): void
    {
        [$result, $expected] = $this->format($sourceFile, $expectedFile, $settings);

        if ($result !== $expected) {
            echo $result, "\n";
        }

        self::assertEquals($expected, $result, $message);
    }

    protected function format(string $sourceFile, string $expectedFile, Settings $settings): array
    {
        $formatterManager = service(FormatterManager::class);
        $source = file_get_contents(__DIR__ . '/../TestFiles/' . $sourceFile . '.php');

        $expected = file_get_contents(__DIR__ . '/../TestFiles/' . $expectedFile . '.php');

        return [$formatterManager->format($source, $settings), $expected];
    }

    protected function assertChanges(string $sourceFile, string $targetFile, Settings $settings)
    {
        $this->compare($sourceFile, $targetFile, $settings, "$sourceFile => $targetFile");
    }

}
