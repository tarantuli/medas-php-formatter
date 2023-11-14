<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Psr12;

class Psr12Test extends BaseTestClass
{
    public function testIfElse(): void
    {
        $this->assertRemainsTheSame('psr12/ifelse', new Psr12());
    }

    public function testVisibility(): void
    {
        $this->assertChanges('psr12/visibility-pre', 'psr12/visibility-post', new Psr12());
    }

    public function testSwitch(): void
    {
        $this->assertRemainsTheSame('psr12/switch-statement', new Psr12());
    }
}
