<?php

abstract class Base
{
    public function run(): void
    {
        echo 1;
    }

    abstract public string $label { get; set; }
}
