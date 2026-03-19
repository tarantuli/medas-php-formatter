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

    public function testBugs1(): void
    {
        $this->assertRemainsTheSame('attributes/bugs1');
    }

    public function testBugs2(): void
    {
        $this->assertRemainsTheSame('attributes/bugs2');
    }

    public function testBug3(): void
    {
        $this->assertRemainsTheSame('attributes/bug3');
    }
}
