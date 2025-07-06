<?php

/**
 * Retrieve the URL of an image by its file path.
 *
 * If the file exists in the public storage, its URL is returned.
 * Can optionally return the URL of a thumbnail version of the image.
 * If the file does not exist, a placeholder image URL is returned.
 *
 * @param string|null $path The file path of the image.
 * @param bool|null $isThumbnail Whether to return the URL for a thumbnail. Defaults to false.
 * @return string The URL of the image or a placeholder image.
 */
function getImageUrlByPath(?string $path, ?bool $isThumbnail = false): string
{
    if (
        !empty($path)
        && Illuminate\Support\Facades\Storage::disk('public')->exists($path)
    ) {
        return Illuminate\Support\Facades\Storage::url(
            ($isThumbnail ? 'thumbnails/' : '') . $path
        );
    }

    return asset('assets/images/placeholder.jpg');
}
