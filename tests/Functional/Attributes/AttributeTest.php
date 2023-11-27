<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Attributes;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class AttributeTest extends BaseTestClass
{
    public function testAttributes(): void
    {
        $this->assertRemainsTheSame('attributes/attributes');
    }

    public function testComplexPromotedProperties(): void
    {
        $this->assertRemainsTheSame('attributes/complex-promoted-properties');
    }
}
