<?php

/**
 * Generate a pseudo-random UUID string of hexadecimal characters.
 */
function getUuid(int $length = 6): string
{
    return bin2hex(random_bytes($length - 1));
}

/**
 * Truncates a given string to a maximum length and appends an ellipsis if
 * the string exceeds the maximum length.
 */
function addEllipsis(string $text, int $max = 35): string
{
    return strlen($text) > $max ? mb_substr($text, 0, $max, 'UTF-8') . '...' : $text;
}

/**
 * Convert a given string from underscore notation to a human-readable format.
 */
function humanize(string $string): string
{
    if (empty($string)) return '';

    return ucwords(str_replace('_', ' ', $string));
}

/**
 * Convert a given string from a human-readable format to underscore notation.
 */
function machinize(string $string): string
{
    if (empty($string)) return '';

    return strtolower(str_replace(' ', '_', trim($string)));
}

/**
 * Format a given amount as a currency string.
 */
function currency(float|null $amount = null, $withCurrencySymbol = true): string|float
{
    if (is_null($amount)) {
        $amount = 0.00;
    }

    $amount = number_format(
        $amount,
        config('currency.decimal'),
        config('currency.decimal_separator'),
        config('currency.thousand_separator'),
    );

    if (! $withCurrencySymbol) return $amount;

    return config('currency.is_symbol_prefixed') == 'true'
        ? config('currency.symbol') . $amount
        : $amount . config('currency.symbol');
}

/**
 * Returns the initials of a full name.
 */
function getNameInitials(string $name): string
{
    $words = explode(' ', $name);

    if (count($words) > 1) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    return strtoupper(substr($words[0], 0, 1));
}
