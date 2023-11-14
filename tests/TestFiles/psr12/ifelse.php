<?php

if ($a === true) {
    $b = 10;
} elseif ($a === false) {
    $b = 100;
} else {
    $b = 10;
}

do {
    echo '!';
} while ($b < 10);

while ($b > 0) {
    echo '.';
}
