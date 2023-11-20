<?php

$a = $b ? $c : $d;
$a = true ? $c : $d;
$a = $b ? false : $d;
$a = $b ? $c : null;
$a = $b ?: $c;
$a = true ?: $c;
$a = $b ?: false;
$a = $b ?? $c;
$a = true ?? $c;
$a = $b ?? false;

class A
{
    public int|null $c;

    public function a(string|null $b): string
    {
    }

    public function b(): string|null
    {
    }

    public function getAutoClassesDirectory(): string
    {
        return $this->autoClassesDirectory
            ?: sprintf('%s%sAutoClasses', $this->getTargetBaseDirectory(), DIRECTORY_SEPARATOR);
    }
}
