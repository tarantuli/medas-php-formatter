<?php

$var = match (expression()) {
    true => $a,
    false => fn($a) => $a,
    fn($b) => null,
    ($i > 100) => 2,
    ($i < -100) => mb_strlen(2),
    $i >= 10 => self::create(),
    $i <= 10 => $this,
    $a, $b, $c => 'cheese',
    3 => 'three',
    a($a) || b($b) => 0,
    default => throw new \Exception('oops'),
};
