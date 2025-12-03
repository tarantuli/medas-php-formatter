<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\LineSplitting;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class LongLinesTest extends BaseTestClass
{
    public function testSplitting(): void
    {
        $this->assertRemainsTheSame('line-splitting/long-lines');
    }

    public function testSplitting2(): void
    {
        $this->assertRemainsTheSame('line-splitting/long-lines2');
    }

    public function testSplitting3(): void
    {
        $this->assertRemainsTheSame('line-splitting/long-lines3');
    }

    public function testSplitting4(): void
    {
        $this->assertRemainsTheSame('line-splitting/long-lines4');
    }

    public function testSplitting5(): void
    {
        $this->assertRemainsTheSame('line-splitting/long-lines5');
    }

    public function testKeepOriginalOperatorBreaks(): void
    {
        $this->assertRemainsTheSame('line-splitting/keep-original-operator-breaks');
    }

    public function testFatArrows(): void
    {
        $this->assertRemainsTheSame('line-splitting/fat-arrows');
    }

    public function testMatches(): void
    {
        $this->assertRemainsTheSame('line-splitting/matches');
    }

    public function testBug1(): void
    {
        $this->assertRemainsTheSame('line-splitting/bug1');
    }
    public function testSlashInQuote(): void
    {
        $this->assertRemainsTheSame('line-splitting/slash-in-quote');
    }
}

