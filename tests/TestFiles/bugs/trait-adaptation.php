<?php

declare(strict_types=1);

class TraitAdaptation
{
    use A, B {
        A::foo insteadof B;
        B::foo as bar;
    }
}
