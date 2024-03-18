<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

class BugsTest extends BaseTestClass
{
    public function testBugs1(): void
    {
        $this->assertRemainsTheSame('bugs/bugs1');
    }

    public function testBugs2(): void
    {
        $this->assertRemainsTheSame('bugs/bugs2');
    }

    public function testBugs3(): void
    {
        $this->assertChanges('bugs/bugs3-pre', 'bugs/bugs3-post');
    }

    public function testBugs4(): void
    {
        $this->assertRemainsTheSame('bugs/bugs4');
    }

    public function testBugs5(): void
    {
        $this->assertRemainsTheSame('bugs/bugs5');
    }

    public function testBugs6(): void
    {
        $this->assertRemainsTheSame('bugs/bugs6');
    }

    public function testBugs7(): void
    {
        $this->assertRemainsTheSame('bugs/bugs7');
    }

    public function testBugs8(): void
    {
        $this->assertChanges('bugs/bugs8-pre', 'bugs/bugs8-post');
    }

    public function testBugs9(): void
    {
        $this->assertRemainsTheSame('bugs/bugs9');
    }

    public function testBugs10(): void
    {
        $this->assertRemainsTheSame('bugs/bugs10');
    }

    public function testBugs11(): void
    {
        $this->assertRemainsTheSame('bugs/bugs11');
    }

    public function testBugs12(): void
    {
        $this->assertChanges('bugs/bugs12-pre', 'bugs/bugs12-post');
    }

    public function testEmptyTrait(): void
    {
        $this->assertRemainsTheSame('bugs/empty-trait');
    }

    public function testCommentAfterStart(): void
    {
        $this->assertRemainsTheSame('bugs/comment-after-start');
    }

    public function testShorterStringsSortEarlier(): void
    {
        $this->assertRemainsTheSame('bugs/shorter-strings-sort-earlier');
    }

    public function testNonBlockCurlyBraces(): void
    {
        $this->assertRemainsTheSame('bugs/non-block-curly-braces');
    }
}
