<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Sorting;

use Medas\PhpFormatter\Settings\Medas;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ServiceConstructionParametersTest extends BaseTestClass
{
    public function testSortParameters(): void
    {
        $this->assertChanges('sorting/sort-parameters-pre', 'sorting/sort-parameters-post', new Medas());
    }
}
