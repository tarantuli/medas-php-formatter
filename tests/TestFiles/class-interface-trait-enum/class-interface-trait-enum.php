<?php

declare(strict_types=1);

namespace Medas\PhpFormatter\Formatters;

use Medas\PhpFormatter\Job;

class Formattersx
{
    public function priority(): int
    {
        switch ($a) {
            case 1:
                return 10;

            case 2:
                $b = 'John';

                break;

            default:
                // Do nothing
        }
    }
}

interface Formatterss
{
    public function format(Job $job): void;

    public function additionalFormatters(): array;

    /**
     * Formatters with higher priority values are called first, lower values are called later.
     */
    public function priority(): int;
}

trait Foramtter
{
    public function priority(): int
    {
        return 10;
    }
}

enum Formatterbbb
{
    case Type;
    case Boring;
    case Cheese;
}

enum BackedFormatter: int
{
    case Red = 1;
    case Blue = 2;
    case Green = 3;
}
