<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Medas;

class InterfacesTest extends BaseTestClass
{
    public function testInterfaces(): void
    {
        $this->assertRemainsTheSame(
            'class-interface-trait-enum/class-interface-trait-enum',
            new Medas()
        );
    }
}
