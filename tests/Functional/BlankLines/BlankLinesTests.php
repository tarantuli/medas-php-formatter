<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\BlankLines;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class BlankLinesTests extends BaseTestClass
{
    public function testBeforeFunctionDeclarations(): void
    {
        $this->assertRemainsTheSame('blank-lines/before-function-declarations');
    }
}
