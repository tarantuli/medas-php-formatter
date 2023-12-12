<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Grouping;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class GroupingTest extends BaseTestClass
{
    public function testObjectGrouping(): void
    {
        $this->assertRemainsTheSame('grouping/object-grouping');
    }
}
