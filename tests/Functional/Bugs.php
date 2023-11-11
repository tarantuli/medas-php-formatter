<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Medas;

class Bugs extends BaseTestClass
{
    public function testBugs1(): void
    {
        $this->assertRemainsTheSame('bugs/bugs1', new Medas());
    }

    public function testBugs2(): void
    {
        $this->assertRemainsTheSame('bugs/bugs2', new Medas());
    }

    public function testBugs3(): void
    {
        $this->assertChanges('bugs/bugs3-pre', 'bugs/bugs3-post', new Medas());
    }

    public function testBugs4(): void
    {
        $this->assertRemainsTheSame('bugs/bugs4', new Medas());
    }
}
