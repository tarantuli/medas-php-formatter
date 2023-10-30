<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class PropertyTypesTest extends BaseTestClass
{
    public function testPropertyTypes(): void
    {
        $settings = new Medas();

        $this->assertRemainsTheSame(
            'imports/property-types',
            $settings
        );
    }
}
