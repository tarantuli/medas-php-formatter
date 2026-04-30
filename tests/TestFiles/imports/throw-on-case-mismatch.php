<?php

declare(strict_types=1);

use Medas\Cache\FileSystemCache;
use Medas\Core\Interfaces\CacheManager;
use Medas\Music\Spotify\Api;

service(CacheManager::class)->register(
    new FilesystemCache('var/file-info-cache'),
    'file-info-cache'
);

$refreshTokenFile = __DIR__ . '/../logs/spotify-refresh-token.txt';
$refreshToken = file_exists($refreshTokenFile) ? trim(file_get_contents($refreshTokenFile)) : '';

service(Api::class)->setRefreshToken($refreshToken);
