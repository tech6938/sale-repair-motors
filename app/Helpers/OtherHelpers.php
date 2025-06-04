<?php

/**
 * Decodes a given string as JSON if possible, otherwise returns the string unchanged.
 */
function optionalJsonDecode($data)
{
    if (!is_string($data)) {
        return $data;
    }

    $decodedData = json_decode($data);

    if (json_last_error() === JSON_ERROR_NONE) {
        return $decodedData;
    }

    return $data;
}

/**
 * Convert a file to base64 data URI format.
 */
function base64File(string $path): ?string
{
    if (empty($path)) return null;

    // Try storage first
    if (
        Illuminate\Support\Facades\Storage::disk('public')->exists($path)
    ) {
        $file = Illuminate\Support\Facades\Storage::disk('public')->get($path);
        $mimeType = Illuminate\Support\Facades\Storage::disk('public')->mimeType($path);
    }

    // Fall back to public directory
    elseif (file_exists(public_path($path))) {
        $file = file_get_contents(public_path($path));
        $mimeType = mime_content_type(public_path($path));
    }

    // File not found in either location
    else {
        return null;
    }

    if (empty($file)) return null;

    return sprintf(
        'data:%s;base64,%s',
        $mimeType,
        base64_encode($file)
    );
}
