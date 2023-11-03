<?php

// Incrementers
$a = $b++ + ++$c;
$a = ++$b + ++$c;
$a = $b++ + $c++;
$a = ++$b + $c++;

// Decrementers
$a = $b-- - --$c;
$a = --$b - --$c;
$a = $b-- - $c--;
$a = --$b - $c--;

// Basic integers
$a = -1;
$a = 1 - 1;
$a = 1 - +1;
$a = 1 + -1;
$a = -1 - 1;
$a = -1 - +1;
$a = -1 + -1;

// Basic floats
$a = -1;
$a = 1.0 - 1.0;
$a = 1.0 - +1.0;
$a = 1.0 + -1.0;
$a = -1.0 - 1.0;
$a = -1.0 - +1.0;
$a = -1.0 + -1.0;

// For loop
for ($a = -1; $a < 10; ++$a) {
}

// Keywords and parentheses
return -100;
1 + 2;
($a) + 1;
-($a);
-($a + $b);
$a - ($b);
