<?php

/**
 * Retrieve the URL for an image stored in the public disk.
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
