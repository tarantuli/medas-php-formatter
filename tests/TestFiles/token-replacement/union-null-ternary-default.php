<?php

declare(strict_types=1);

class TernaryDefault
{
    public function f(int $x = FOO ? BAR : BAZ): void
    {
    }
}
