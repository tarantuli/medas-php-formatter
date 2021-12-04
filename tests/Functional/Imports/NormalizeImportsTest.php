<?php

declare(strict_types=1);

namespace Medas\Test\Functional\Imports;

use Medas\PhpBeautifier\Settings\Medas;
use Medas\Test\Functional\BaseTest;

class NormalizeImportsTest extends BaseTest
{
    public function testNormalize(): void
    {
        $this->assertChanges(
            'imports/normalize-imports-pre',
            'imports/normalize-imports-post',
            new Medas()
        );
    }
}
