<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\PhpMode;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ShortOpeningTagTest extends BaseTestClass
{
    public function testShortOpeningTags(): void
    {
        $this->assertRemainsTheSame('php-mode/short-opening-tag');
    }
}
