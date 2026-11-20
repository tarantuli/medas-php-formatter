<?php

declare(strict_types=1);

namespace App;

use Lib\Alpha;
use Other\Beta;

class MultipleImports
{
    public function f(Alpha $alpha): Beta
    {
    }
}
