<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

class StringTest extends BaseTestClass
{
    public function testHeredoc(): void
    {
        $this->assertRemainsTheSame('strings/heredoc');
    }

    public function testComplex(): void
    {
        $this->assertRemainsTheSame('strings/complex');
    }
}
