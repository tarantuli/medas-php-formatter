<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Preparsers\ReorderClassElements;

class Properties
{
    // isUse
    public const int IS_USE_STATEMENT = 0;
    public const int IS_NOT_USE_STATEMENT = 1;

    // isAbstract
    public const int IS_ABSTRACT = 0;
    public const int IS_NOT_ABSTRACT = 1;

    // isConst
    public const int IS_CONST = 0;
    public const int IS_NOT_CONST = 1;

    // isStatic
    public const int IS_STATIC = 0;
    public const int IS_NOT_STATIC = 1;

    // isMethod
    public const int IS_NOT_METHOD = 0;
    public const int IS_METHOD = 1;

    // isMagicMethod
    public const int IS_CONSTRUCTOR = 0;
    public const int IS_DESTRUCTOR = 1;
    public const int IS__TO_STRING = 2;
    public const int IS___SET = 3;
    public const int IS___GET = 4;
    public const int IS_OTHER_MAGIC_METHOD = 5;
    public const int IS_NOT_MAGIC_METHOD = 6;

    // accessModifier
    public const int IS_PUBLIC = 0;
    public const int IS_PROTECTED = 1;
    public const int IS_PRIVATE = 2;
}
