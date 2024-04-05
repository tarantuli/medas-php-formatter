<?php

namespace MyNamespace;

use Absolute\ClassName;
use Absolute\Path\To\AnotherInterface;
use Absolute\ReturnType;
use Clashing\MyClass as ClashMyClass;
use My\Clashbash\ClassName as ClashClassName1;
use My\Clashing\ClassName as ClashClassName;
use MyNamespace\This\Relative\Path\Is\Too\Deep;
use N\Attributes\Types as Type;
use So\Many\Layers\Namespace as AliasedNamespace;
use YetAnotherNamespace\ClassName as AliasOfClassName;
use Your\Clashing\ClassName as ClashClassName2;

class MyClass extends ChildNamespace\BaseClass implements Interfaces\MyInterface, AnotherInterface
{
    use AnotherNamespace\TraitName;
    use Comma, Separated, Traits;

    #[Type\Integer]
    private int $id;

    #[Type\Text]
    private string $name;

    public function a(Relative\ClassName $relativeClass): Relative\ReturnType
    {
        if ($relativeClass instanceof AliasedNamespace\SemiRelativeClass) {
            return false;
        }

        Deep::class;

        return AlsoRelative\ClassName::class;
    }

    public function b(ClassName $absoluteClass): ReturnType
    {
        return [new AliasOfClassName, new NewWithParentheses()];
    }

    public function c(ClassA|ClassB $aClass): ClassC|ClassD|null
    {
        return [
            ClashClassName::class,
            ClashClassName1::class,
            ClashClassName2::class,
            ClashMyClass::class,
        ];
    }

    public function d(StringClass $stringClass): StringClass
    {
        return StringClass::class;
    }

    public function e(
        int    $int,
        string $string,
        array  $array,
        object $object,
        bool   $bool
    ): int|string|array|object|bool|null
    {
        return [\ReflectionClass::class, ChildPath\ThisCanBeInlined::class];
    }
}
