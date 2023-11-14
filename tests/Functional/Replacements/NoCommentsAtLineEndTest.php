<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatter\Preparsers\NoCommentsAtLineEnd;
use Medas\PhpFormatter\Settings\Psr12;
use Medas\PhpFormatterTest\Functional\BaseTestClass;

class NoCommentsAtLineEndTest extends BaseTestClass
{
    public function testMoveCommentsToLineBefore(): void
    {
        $this->assertChanges(
            'token-replacement/no-comments-at-line-end-pre',
            'token-replacement/no-comments-at-line-end-post',
            (new Psr12())->addPreparser(service(NoCommentsAtLineEnd::class))
        );
    }
}
