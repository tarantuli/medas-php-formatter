<?php

declare(strict_types=1);

namespace Medas\ServiceManager;

use Medas\ServiceManager\Attributes\Service;
use Medas\ServiceManager\Attributes\Mervice as Pervice;

use function A\B;
use function A\C;
use function A\D;
use function B\E;

use const C;

#[Service(12)]
abstract class ServiceInstantiator
{
    private const CONST_NAME = [1, 2, 3, 4, 5];

    public ?string $cheese=NULL;

    public function callback(int &$a, ?array $b = [], bool &...$questions): ?callable
    {
        return fn($a) => strlen($a);
    }

    public function ternary(): bool{
        $i = $a ? true : false;
        $j = $a ?: false;
    }

    Protected Function test(RelativePath\RelativeClass $relativeClass, bool $isDefault = false, callable ...$callableArray)
    {
        printf("\e[%sm%s\e[0m", implode(';', $formats), $string);

        for ($i = 0; $i < 10; ++$i) {
            // Test
        }

        foreach ($relativeClass as $key => $value) {
            $value += 2;
            $value -= 2;
            $value *= 2;
            $value /= 2;
            $value %= null;
        }

        while ($condition === TRUE && $value === 1278934987 && $key === 'a reasonably long string that pushes the length of the line over 120') {
            // Test
        }

        do {
            // Test
        } while ($condition === true);

        switch ($isDefault) {
            case true:
                // Test
                break;

            case 1:
            case 2:
                // Test
                break;

            case 3: {
                // Test
            }

            default:
                // Test
        }

        $result = match ($condition) {
            true => 1,
            ($i > 100) => 2,
            ($i < -100) => mb_strlen(2),
            ($i >= 10) => self::create(),
            ($i <= 10) => $this,
            default => throw new \Exception('oops'),
        };

        return $result;
    }

    final public static function create(): static
    {
        return new static();
    }
}
