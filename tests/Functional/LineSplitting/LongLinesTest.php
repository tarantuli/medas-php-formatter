<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\LineSplitting;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class LongLinesTest extends BaseTestClass
{
    public function testSplitting(): void
    {
        $this->assertRemainsTheSame(
            'line-splitting/long-lines',
            new Medas()
        );
    }

    public function testSplitting2(): void
    {
        $this->assertRemainsTheSame(
            'line-splitting/long-lines2',
            new Medas()
        );
    }

    public function testSplitting3(): void
    {
        $this->assertRemainsTheSame(
            'line-splitting/long-lines3',
            new Medas()
        );
    }

    public function testKeepOriginalOperatorBreaks(): void
    {
        $this->assertRemainsTheSame(
            'line-splitting/keep-original-operator-breaks',
            new Medas()
        );
    }
}
