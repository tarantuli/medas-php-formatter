<?php

declare(strict_types=1);

namespace Medas\PhpFormatterTest\Functional;

class FormatterTest extends BaseTestClass
{
    public function testEmptyMethodBody(): void
    {
        $this->assertRemainsTheSame('empty-method-body');
    }

    public function testMedasIfElse(): void
    {
        $this->assertRemainsTheSame('if-else');
    }

    public function testPlusAndMinus(): void
    {
        $this->assertRemainsTheSame('plus-and-minus');
    }

    public function testTernaryExpressions(): void
    {
        $this->assertChanges('ternary-expressions-pre', 'ternary-expressions-post');
    }

    public function testMethodsAndFunctions(): void
    {
        $this->assertRemainsTheSame('methods-and-functions');
    }

    public function testParentheses(): void
    {
        $this->assertRemainsTheSame('parentheses');
    }

    public function testSquareBrackets(): void
    {
        $this->assertRemainsTheSame('square-brackets');
    }

    public function testMatch(): void
    {
        $this->assertRemainsTheSame('match');
    }

    public function testSwitch(): void
    {
        $this->assertRemainsTheSame('switch-statement');
    }

    public function testComments(): void
    {
        $this->assertRemainsTheSame('comments');
    }

    public function testNamedArguments(): void
    {
        $this->assertRemainsTheSame('named-arguments');
    }

    public function testBasicPreformatted(): void
    {
        $this->assertRemainsTheSame('basic-preformatted');
    }

    public function testPipes(): void
    {
        $this->assertRemainsTheSame('pipes');
    }

    public function testStatementTypeGrouping(): void
    {
        $this->assertRemainsTheSame('group-statement-types');
    }

    public function testLambdaFunctions(): void
    {
        $this->assertRemainsTheSame('lambda-functions');
    }

    public function testClassProperties(): void
    {
        $this->assertRemainsTheSame('class-properties');
    }

    public function testNoSingleLineControlBodies(): void
    {
        $this->assertChanges(
            'token-replacement/no-single-line-control-bodies-pre',
            'token-replacement/no-single-line-control-bodies-post'
        );
    }

    public function testAmpersands(): void
    {
        $this->assertRemainsTheSame('ampersands');
    }
}
