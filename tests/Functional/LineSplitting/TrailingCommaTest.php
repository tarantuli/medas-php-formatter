<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\LineSplitting;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class TrailingCommaTest extends BaseTestClass
{
    public function testTrailingComma(): void
    {
        $this->assertRemainsTheSame('line-splitting/line-splitting');
    }

    public function testAttributes(): void
    {
        $this->assertRemainsTheSame('line-splitting/line-splitting-with-attributes');
    }
}
