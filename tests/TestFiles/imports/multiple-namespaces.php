<?php

declare(strict_types=1);

namespace First {
    use Lib\Helper;

    class A
    {
        public function f(): Helper
        {
        }
    }
}

namespace Second {
    use Other\Thing;

    class B
    {
        public function g(): Thing
        {
        }
    }
}
