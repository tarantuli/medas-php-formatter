<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ImportBugsTest extends BaseTestClass
{
    public function testBugRelativeAlias1(): void
    {
        $this->assertRemainsTheSame('imports/bug-relative-alias');
    }

    public function testBugRelativeAlias2(): void
    {
        $this->assertRemainsTheSame('imports/bug-relative-alias-2');
    }
}
