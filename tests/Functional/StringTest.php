<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Medas;

class StringTest extends BaseTestClass
{
    public function testHeredoc(): void
    {
        $this->assertRemainsTheSame('strings/heredoc', new Medas());
    }

    public function testComplex(): void
    {
        $this->assertRemainsTheSame('strings/complex', new Medas());
    }
}
