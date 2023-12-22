<?php

declare(strict_types=1);

use Exceptions;
use Shared\Cmd\Colors;

$directoryIteratorFlags = FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_FILEINFO;

try {
    $this->processFile($file, $sourceDirectory);
}
catch (
    Exceptions\FileAnalysisException
    |Exceptions\FileHasNoArtistException
    |Exceptions\FileHasNoTitleException
    |Exceptions\FilenameUnreadableByHttpqException $exception
) {
    Colors::printColorized(sprintf("  × %s\n", $exception->getMessage()), Colors::RED);
}
catch (Exceptions\FileAnalysisException|Exceptions\FileHasNoArtistException $exception) {
    Colors::printColorized(sprintf("  × %s\n", $exception->getMessage()), Colors::RED);
}
catch (Exceptions\FileAnalysisException|Exceptions\FileHasNoArtistException) {
    Colors::printColorized(sprintf("  × %s\n", $exception->getMessage()), Colors::RED);
}

function a(int|string $element): void
{
    // Lalala
}

class A
{
    private int|float $john;

    // Lalala
    private string $cantHaveFloatingComments;
}
