<?php

declare(strict_types=1);

if ($foregroundColor) {
    echo "\033[{$foregroundColor}m";
}

if ($backgroundColor) {
    echo "\033[{$backgroundColor}m";
}

echo $string, "\033[0m";
