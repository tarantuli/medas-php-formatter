<?php

switch ($a) {
    case 1:
    case 2:
        return 'cheese';

    case 3:
        echo 'sugar';
        break;

    case $a ? $b : $c :
        return 'ternary';

    case 'John':
        foreach ($b as $c) {
            print($c);
        }
        break;

    case 'Erik':
        switch ($b) {
            case 1:
            case 2:
                return 'bread';
            default:
                return 'egg';
        }
    default:
        throw new \Exception('invalid $a');
}
