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

    // The arrow of an arrow function that is itself the value after an array key's arrow belongs to that value, so
    // when both are split it goes one level deeper; it came out at the same level as the key's arrow
    public function testNestedFatArrowsAreSplitOneLevelDeeper(): void
    {
        $this->assertChanges('line-splitting/nested-fat-arrows-pre', 'line-splitting/nested-fat-arrows-post');
    }

    public function testNestedFatArrowsKeepTheirDeeperIndentation(): void
    {
        $this->assertRemainsTheSame('line-splitting/nested-fat-arrows-post');
    }

    // A part of a split array item that is split again gets no blank line before it; one was added in between the
    // arrow function's line and its body when the body was split on its concatenations
    public function testNoBlankLineInsideSplitArrayItem(): void
    {
        $this->assertChanges(
            'line-splitting/no-blank-line-inside-split-array-item-pre',
            'line-splitting/no-blank-line-inside-split-array-item-post'
        );
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
