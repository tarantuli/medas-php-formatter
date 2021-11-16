<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\PhpBeautifier;
use Medas\PhpBeautifier\Settings\Settings;
use PHPUnit\Framework\TestCase;

class ReformatterTest extends TestCase
{
    /** @dataProvider getTestFiles */
    public function testReformatter(string $sourceFile, string $expectedFile, Settings $settings = null): void
    {
        $phpReformatter = sm()->resolve(PhpBeautifier::class);
        $source = file_get_contents(__DIR__ . '/../TestFiles/' . $sourceFile);

        $expected = file_get_contents(__DIR__ . '/../TestFiles/' . $expectedFile);

        $result = $phpReformatter->beautify($source, $settings);

        self::assertEquals($expected, $result, "$sourceFile => $expectedFile");
    }

    public function getTestFiles(): array
    {
        return [
            ['basic-preformatted.php', 'basic-preformatted.php'],
        ];
    }
}
