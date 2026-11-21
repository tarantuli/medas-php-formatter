<?php

$shape = new class(1) extends Base implements Shape {
    public function __construct(public int $x)
    {
    }
};

$plain = new class($a, $b) implements Shape {
};
