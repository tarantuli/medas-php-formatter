<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class PropertyTypesTest extends BaseTestClass
{
    public function testPropertyTypes(): void
    {
        $this->assertRemainsTheSame('imports/property-types');
    }
}
