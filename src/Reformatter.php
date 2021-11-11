<?php

declare(strict_types=1);

namespace Medas\PhpReformatter;

use Medas\PhpReformatter\Exceptions\ReformattedCodeIsInvalidException;

class Reformatter
{
    /** @var \PhpToken[] $tokens */
    public function __construct(private array $tokens, private Settings\Settings $settings)
    {
    }

    public function reformat(): string
    {
        $result = 'done!';
        $this->assertCodeIsValid($result);
        return $result;
    }

    private function assertCodeIsValid(string $code): void
    {
        $validator = sm()->resolve(Validator::class);

        if (!$validator->validate($code)) {
            throw new ReformattedCodeIsInvalidException($code, $validator->getErrorMessage());
        }
    }
}
