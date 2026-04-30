<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Imports;

use Medas\PhpClassAnalysis\Exceptions\ImportLabelCaseMismatch;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class ImportBugsTest extends BaseTestClass
{
    public function testBugRelativeAlias1(): void
    {
        $this->assertRemainsTheSame('imports/bug-relative-alias');
    }

    public function testBugRelativeAlias2(): void
    {
        $this->assertRemainsTheSame('imports/bug-relative-alias-2');
    }

    public function testBugOrGroupedCatches(): void
    {
        $this->assertRemainsTheSame('imports/or-grouped-catches');
    }

    public function testDocblockArrayType(): void
    {
        $this->assertRemainsTheSame('imports/docblock-array-type');
    }

    public function testThrowOnCaseMismatch(): void
    {
        $this->expectException(ImportLabelCaseMismatch::class);
        $this->assertRemainsTheSame('imports/throw-on-case-mismatch');
    }

    public function testUselessImportBug(): void
    {
        $this->assertChanges('imports/useless-import-bug-pre', 'imports/useless-import-bug-post');
    }
}
