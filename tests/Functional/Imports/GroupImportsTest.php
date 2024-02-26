<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class GroupImportsTest extends BaseTestClass
{
    public function testGrouping(): void
    {
        $settings = new Medas();

        $settings->import->maxRelativeDepth = 3;

        $this->assertRemainsTheSame('imports/group-imports', $settings);
    }
}
