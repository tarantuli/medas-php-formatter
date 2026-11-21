<?php

// Comment
function f(): void
{
    $a = 1;
}

/* Comment */
class A
{
    // Comment
    public function g(): void
    {
        // Comment
        if ($a) {
            $b = 1;
        }
    }
}
