<?php

class A
{
    public function f(string $format): string
    {
        return preg_replace_callback(
            '/([AZ])(\.{3})?|./',
            fn(array $token) => match ($token[1] ?? '') {
                'A' => '[A-Z]' . $this->repeat($token),
                'Z' => '[A-Z0-9]' . $this->repeat($token),
                default => preg_quote($token[0], '/'),
            }, $format);
    }
}
