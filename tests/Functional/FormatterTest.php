<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

use Medas\PhpFormatter\Settings\{Medas};

class FormatterTest extends BaseTestClass
{
    public function testEmptyMethodBody(): void
    {
        $this->assertRemainsTheSame('empty-method-body', new Medas());
    }

    public function testMedasIfElse(): void
    {
        $this->assertRemainsTheSame('if-else', new Medas());
    }

    public function testPlusAndMinus(): void
    {
        $this->assertRemainsTheSame('plus-and-minus', new Medas());
    }

    public function testTernaryExpressions(): void
    {
        $this->assertRemainsTheSame('ternary-expressions', new Medas());
    }

    public function testMethodsAndFunctions(): void
    {
        $this->assertRemainsTheSame('methods-and-functions', new Medas());
    }

    public function testParentheses(): void
    {
        $this->assertRemainsTheSame('parentheses', new Medas());
    }

    public function testSquareBrackets(): void
    {
        $this->assertRemainsTheSame('square-brackets', new Medas());
    }

    public function testMatch(): void
    {
        $this->assertRemainsTheSame('match', new Medas());
    }

    public function testSwitch(): void
    {
        $this->assertRemainsTheSame('switch-statement', new Medas());
    }

    public function testComments(): void
    {
        $this->assertRemainsTheSame('comments', new Medas());
    }

    public function testNamedArguments(): void
    {
        $this->assertRemainsTheSame('named-arguments', new Medas());
    }

    public function testBasicPreformatted(): void
    {
        $this->assertRemainsTheSame('basic-preformatted', new Medas());
    }

    public function testPipes(): void
    {
        $this->assertRemainsTheSame('pipes', new Medas());
    }

    public function testStatementTypeGrouping(): void
    {
        $this->assertRemainsTheSame('group-statement-types', new Medas());
    }

    public function testLambdaFunctions(): void
    {
        $this->assertRemainsTheSame('lambda-functions', new Medas());
    }

    public function testClassProperties(): void
    {
        $this->assertRemainsTheSame('class-properties', new Medas());
    }

    public function testNoSingleLineControlBodies(): void
    {
        $this->assertChanges(
            'token-replacement/no-single-line-control-bodies-pre',
            'token-replacement/no-single-line-control-bodies-post',
            new Medas()
        );
    }
}
