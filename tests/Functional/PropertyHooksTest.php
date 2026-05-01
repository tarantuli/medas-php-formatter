<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

class PropertyHooksTest extends BaseTestClass
{
    public function testPropertyHooks(): void
    {
        $this->assertRemainsTheSame('property-hooks');
    }
}
