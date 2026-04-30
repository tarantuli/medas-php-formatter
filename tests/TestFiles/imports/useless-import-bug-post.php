<?php

declare(strict_types=1);

use Project\SpotifyTrackFinder;

$settings = new SpotifyTrackFinder\Settings(
    $db,
    $table,
    explode("\n", file_get_contents($autoUnsetAlbumsPath)),
    fn($artist) => file_put_contents($autoUnsetAlbumsPath, "$artist\n", FILE_APPEND),
    $connectedAlbums,
    function ($artist, $album) use ($connectedAlbumsPath) {
        if ($artist === $album) {
            return;
        }

        file_put_contents($connectedAlbumsPath, "$artist|$album\n", FILE_APPEND);
    });

$trackFinder = new SpotifyTrackFinder();
