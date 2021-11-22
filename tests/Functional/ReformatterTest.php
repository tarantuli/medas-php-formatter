<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\PhpBeautifier;
use Medas\PhpBeautifier\Settings\Medas;
use Medas\PhpBeautifier\Settings\Psr12;
use Medas\PhpBeautifier\Settings\Settings;
use PHPUnit\Framework\TestCase;

class ReformatterTest extends TestCase
{
    public function testEmptyMethodBody(): void
    {
        $this->assertRemainsTheSame('empty-method-body', new Medas());
    }

    private function assertRemainsTheSame(string $sourceFile, Settings $settings)
    {
        $this->compare($sourceFile, $sourceFile, $settings, "$sourceFile changed after formatting");
    }

    private function compare(string $sourceFile, string $expectedFile, Settings $settings, string $message): void
    {
        [$result, $expected] = $this->format($sourceFile, $expectedFile, $settings);

        if ($result !== $expected) {
            echo $result, "\n";
        }

        self::assertEquals($expected, $result, $message);
    }

    private function format(string $sourceFile, string $expectedFile, Settings $settings): array
    {
        $phpReformatter = service(PhpBeautifier::class);
        $source = file_get_contents(__DIR__ . '/../TestFiles/' . $sourceFile . '.php');

        $expected = file_get_contents(__DIR__ . '/../TestFiles/' . $expectedFile . '.php');

        return [$phpReformatter->beautify($source, $settings), $expected];
    }

    public function testPsr12IfElse(): void
    {
        $this->assertRemainsTheSame('psr12-ifelse', new Psr12());
    }

    public function testIncDec(): void
    {
        $this->assertRemainsTheSame('inc-dec', new Medas());
    }

    public function testTernaryExpressions(): void
    {
        $this->assertRemainsTheSame('ternary-expressions', new Medas());
    }

    public function testVisibility(): void
    {
        $this->assertChanges('psr12-visibility-pre', 'psr12-visibility-post', new Psr12());
    }

    private function assertChanges(string $sourceFile, string $targetFile, Settings $settings)
    {
        $this->compare($sourceFile, $targetFile, $settings, "$sourceFile => $targetFile");
    }

    public function testBasicPreformatted(): void
    {
        $this->assertRemainsTheSame('basic-preformatted', new Medas());
    }
}
