<?php

namespace MyNamespace;

use AnotherNamespace\ClassName;
use GlobalNamespaceClass;
use Unused\Alias as UnusedAlias;
use N\Attributes\Types as Type;
use MyNamespace\ChildPath\ThisCanBeInlined;
use So\Many\Layers\Namespace as AliasedNamespace;
use YetAnotherNamespace\ClassName as AliasOfClassName;

class MyClass extends ChildNamespace\BaseClass implements Interfaces\MyInterface, \Absolute\Path\To\AnotherInterface
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

        This\Relative\Path\Is\Too\Deep::class;

        return AlsoRelative\ClassName::class;
    }

    public function b(\Absolute\ClassName $absoluteClass): \Absolute\ReturnType
    {
        return [new AliasOfClassName, new NewWithParentheses()];
    }

    public function c(ClassA|ClassB $aClass): ClassC|ClassD|null
    {
        return [
            \My\Clashing\ClassName::class,
            \My\Clavicle\ClassName::class,
            \Your\Clashing\ClassName::class,
            \Clashing\MyClass::class,
        ];
    }

    public function d(StringClass $stringClass): StringClass
    {
        return StringClass::class;
    }

    public function e(int $int, string $string, array $array, object $object, bool $bool): int|string|array|object|bool|null
    {
        return [\ReflectionClass::class, ThisCanBeInlined::class];
    }
}
