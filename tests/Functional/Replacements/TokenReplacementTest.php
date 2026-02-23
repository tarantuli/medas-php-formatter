<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional\Replacements;

use Medas\PhpFormatterTest\Functional\BaseTestClass;

class TokenReplacementTest extends BaseTestClass
{
    public function testDieInsteadOfExit(): void
    {
        $settings = new UseDieInsteadOfExitSettings();

        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/die-instead-of-exit-post',
            $settings,
            'die() instead of exit()'
        );
    }

    public function testExitInsteadOfDie(): void
    {
        $settings = new UseExitInsteadOfDieSettings();

        $this->compare(
            'token-replacement/die-and-exit',
            'token-replacement/exit-instead-of-die-post',
            $settings,
            'exit() instead of die()'
        );
    }

    public function testImplodeInsteadOfJoin(): void
    {
        $settings = new UseImplodeInsteadOfJoinSettings();

        $this->compare(
            'token-replacement/implode-and-join',
            'token-replacement/implode-instead-of-join-post',
            $settings,
            'implode() instead of join()'
        );
    }

    public function testUnionNullInsteadOfNullable(): void
    {
        $settings = new UseUnionNullInsteadOfNullableSettings();

        $this->compare(
            'token-replacement/union-null-pre',
            'token-replacement/union-null-post',
            $settings,
            'union null instead of nullable ?'
        );
    }
}
