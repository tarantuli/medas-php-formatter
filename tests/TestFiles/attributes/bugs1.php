<?php

declare(strict_types=1);

class ResponseController
{
    public function __construct(
        #[ConfigValue(CookieJarFile::class)]
        string|null $cookieJarFile,
    )
    {
    }

    public function create(string $response, #[ArrayShape(Curl::TRANSFER_INFO_SHAPE)] array $info): Response
    {
    }
}
