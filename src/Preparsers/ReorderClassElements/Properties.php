<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

class Properties
{
    // isUse
    public const IS_USE_STATEMENT = 0;
    public const IS_NOT_USE_STATEMENT = 1;

    // isAbstract
    public const IS_ABSTRACT = 0;
    public const IS_NOT_ABSTRACT = 1;

    // isConst
    public const IS_CONST = 0;
    public const IS_NOT_CONST = 1;

    // isStatic
    public const IS_STATIC = 0;
    public const IS_NOT_STATIC = 1;

    // isMethod
    public const IS_NOT_METHOD = 0;
    public const IS_METHOD = 1;

    // isMagicMethod
    public const IS_CONSTRUCTOR = 0;
    public const IS_DESTRUCTOR = 1;
    public const IS__TO_STRING = 2;
    public const IS___SET = 3;
    public const IS___GET = 4;
    public const IS_OTHER_MAGIC_METHOD = 5;
    public const IS_NOT_MAGIC_METHOD = 6;

    // accessModifier
    public const IS_PUBLIC = 0;
    public const IS_PROTECTED = 1;
    public const IS_PRIVATE = 2;
}
