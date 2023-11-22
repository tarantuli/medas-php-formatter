<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\Medas;

class BugsTest extends BaseTestClass
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

    public function testBugs5(): void
    {
        $this->assertRemainsTheSame('bugs/bugs5', new Medas());
    }

    public function testBugs6(): void
    {
        $this->assertRemainsTheSame('bugs/bugs6', new Medas());
    }

    public function testBugs7(): void
    {
        $this->assertRemainsTheSame('bugs/bugs7', new Medas());
    }

    public function testBugs8(): void
    {
        $this->assertChanges('bugs/bugs8-pre', 'bugs/bugs8-post', new Medas());
    }

    public function testBugs9(): void
    {
        $this->assertRemainsTheSame('bugs/bugs9', new Medas());
    }

    public function testBugs10(): void
    {
        $this->assertRemainsTheSame('bugs/bugs10', new Medas());
    }

    public function testBugs11(): void
    {
        $this->assertRemainsTheSame('bugs/bugs11', new Medas());
    }

    public function testEmptyTrait(): void
    {
        $this->assertRemainsTheSame('bugs/empty-trait', new Medas());
    }

    public function testCommentAfterStart(): void
    {
        $this->assertRemainsTheSame('bugs/comment-after-start', new Medas());
    }

    public function testShorterStringsSortEarlier(): void
    {
        $this->assertRemainsTheSame('bugs/shorter-strings-sort-earlier', new Medas());
    }

    public function testNonBlockCurlyBraces(): void
    {
        $this->assertRemainsTheSame('bugs/non-block-curly-braces', new Medas());
    }
}
