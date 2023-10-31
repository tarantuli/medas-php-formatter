<?php

declare(strict_types=1);

#[\ClassAttribute]
class ClassName
{
    #[\PropertyAttribute]
    private string $property;

    #[\Multiple]
    #[\Attributes]
    private int $amount;

    #[\WithValues(\SomeClassName::class)]
    #[\WithValues(10)]
    private bool $true;

    /**
     * @var string
     */
    #[\AfterDoccomment]
    private string $comments;

    #[\BeforeDoccomment]
    /**
     * @var string
     */
    private string $altComments;

    public function __construct(
        #[\PromotedPropertyAttribute]
        public readonly \ClassName $parentClass,
    )
    {
    }

    #[\MethodAttribute]
    public function testMethod(): void
    {
        // Do nothing
    }
}
