<?php

declare(strict_types=1);

class FatArrows
{
    private const CHARACTER_SET_STRINGS = [
        self::NUMERIC => '0123456789',
        self::ALPHANUMERIC_CS => '0123456789abcdefghjklmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ',
        self::ALPHANUMERIC_CI => '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ',
        self::ALPHABETIC_CS => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::ALPHABETIC_CI => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        self::PRINTABLE_CS
            => '!#$%&()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~',
    ];
}
