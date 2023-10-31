<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Attributes;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class AttributeTests extends BaseTestClass
{
    public function testAttributes(): void
    {
        $settings = new Medas();

        $this->assertRemainsTheSame(
            'attributes/attributes',
            $settings
        );
    }

    public function testComplexPromotedProperties(): void
    {
        $settings = new Medas();

        $this->assertRemainsTheSame(
            'attributes/complex-promoted-properties',
            $settings
        );
    }
}
