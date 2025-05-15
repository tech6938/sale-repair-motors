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
