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
    public ?int $c;

    public function a(?string $b): string
    {
    }

    public function b(): ?string
    {
    }
}
