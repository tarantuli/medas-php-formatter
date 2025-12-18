<?php

declare(strict_types=1);

use Medas\Core\Types\BaseType;

class TypedConstants extends BaseType
{
    const int UNSIGNED_1_BYTE_MAX = 255;
    const int UNSIGNED_2_BYTE_MAX = 65535;
    const int UNSIGNED_3_BYTE_MAX = 16777215;
    const array XTERM_BUGS = [];
}
