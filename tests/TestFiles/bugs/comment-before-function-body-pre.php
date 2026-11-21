<?php

function f(): void // Comment
{
    $a = 1;
}

class A /* Comment */
{
    public function g(): void // Comment
    {
        if ($a) // Comment
        {
            $b = 1;
        }
    }
}
