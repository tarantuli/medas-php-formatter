<?php

switch ($a) {
    case 1:
    case 2:
        return 'cheese';

    case 3:
        echo 'sugar';
        break;

    case ($a ? $b : $c):
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

class Askfjlfkj
{
    private static function getImplicitArgumentIdsForFunction(string $function): array
    {
        // Zero based
        switch ($function) {
            case 'headers_sent':
                return [0, 1];

            case 'list':
                return [0, 1, 2, 3, 4, 5, 6];

            case 'exec':
            case 'parse_str':
                return [1];

            case 'preg_match':
            case 'preg_match_all':
                return [2];

            case 'sscanf':
                return [2, 3, 4, 5];

            case 'str_ireplace':
                return [3];

            case 'preg_replace':
                return [4];
        }

        return [];
    }
}
