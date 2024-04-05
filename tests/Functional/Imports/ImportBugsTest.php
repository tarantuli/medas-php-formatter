<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ImportBugsTest extends BaseTestClass
{
    public function testBugRelativeAlias(): void
    {
        $this->assertRemainsTheSame('imports/bug-relative-alias');
    }
}
