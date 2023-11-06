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
}
