<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\LineSplitting;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class TrailingCommaTest extends BaseTestClass
{
    public function testTrailingComma(): void
    {
        $settings = (new Medas());

        $this->assertRemainsTheSame(
            'line-splitting/line-splitting',
            $settings
        );
    }

    public function testAttributes(): void
    {
        $settings = (new Medas());

        $this->assertRemainsTheSame(
            'line-splitting/line-splitting-with-attributes',
            $settings
        );
    }
}
