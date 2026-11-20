<?php

declare(strict_types=1);

class DnfTypes
{
    public function f((A&B)|null $c = null, Foo|null $d = null): (A&B)|null
    {
        return $this->g(name: 1);
    }
}
