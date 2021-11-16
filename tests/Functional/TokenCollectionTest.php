<?php

declare(strict_types=1);

namespace Medas\Test\Functional;

use Medas\PhpBeautifier\Tokens\Token;
use Medas\PhpBeautifier\Tokens\TokenCollection;
use PHPUnit\Framework\TestCase;

class TokenCollectionTest extends TestCase
{
    public function testLoop(): void
    {
        $collection = new TokenCollection('<?php $var = 1;');

        foreach ($collection as $token) {
            self::assertEquals(new Token(T_OPEN_TAG, '<?php ', 1, 0), $token);
            break;
        }
    }
}
