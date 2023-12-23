<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Reordering;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ReorderingTest extends BaseTestClass
{
    public function testBasicReordering(): void
    {
        $this->assertChanges(
            'reordering/abstract-class-pre',
            'reordering/abstract-class-post',
        );
    }

    public function testBug1(): void
    {
        $this->assertRemainsTheSame('reordering/bug1');
    }
}
