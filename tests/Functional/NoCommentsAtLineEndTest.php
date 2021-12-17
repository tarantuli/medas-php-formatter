<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\Preparsers\NoCommentsAtLineEnd;
use Medas\PhpBeautifier\Settings\Psr12;

class NoCommentsAtLineEndTest extends BaseTest
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
