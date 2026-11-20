<?php

declare(strict_types=1);

namespace App;

use Lib\Helper;
use Other\Thing;

/**
 * Only references `Helper` in a docblock.
 *
 * @see Helper
 */
class SeeTagImport
{
    public function f(): Thing
    {
    }
}
