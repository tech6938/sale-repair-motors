<?php

/**
 * Generates a UUID of the given length in hexadecimal format.
 *
 * @param int $length The length of the UUID in bytes. Defaults to 6 bytes.
 * @return string The generated UUID.
 */
function getUuid(int $length = 6): string
{
    return bin2hex(random_bytes($length - 1));
}

/**
 * Add an ellipsis to a given string if it is longer than the specified maximum number of characters.
 *
 * @param string $text The string to add the ellipsis to.
 * @param int $max The maximum number of characters to display before adding an ellipsis.
 * @return string The string with an ellipsis added if it exceeds the maximum characters.
 */
function addEllipsis(string $text, int $max = 35): string
{
    return strlen($text) > $max ? mb_substr($text, 0, $max, 'UTF-8') . '...' : $text;
}

/**
 * Convert a given string from underscore notation to a human-readable format.
 *
 * If the given string is empty, an empty string is returned.
 *
 * @param string $string The string to humanize.
 * @return string The humanized string.
 */
function humanize(string $string): string
{
    if (empty($string)) return '';

    return ucwords(str_replace('_', ' ', $string));
}

/**
 * Convert a given string from a human-readable format to underscore notation.
 *
 * If the given string is empty, an empty string is returned.
 *
 * @param string $string The string to machinize.
 * @return string The machinized string.
 */
function machinize(string $string): string
{
    if (empty($string)) return '';

    return strtolower(str_replace(' ', '_', trim($string)));
}

/**
 * Formats a given amount as a currency string.
 *
 * If no amount is provided, defaults to 0.00. The function formats the amount
 * according to the application's currency configuration, respecting the decimal
 * places, decimal separator, and thousand separator. Optionally includes the
 * currency symbol.
 *
 * @param float|null $amount The amount to format.
 * @param bool $withCurrencySymbol Whether to include the currency symbol in the output.
 * @return string|float The formatted currency string, or the amount as a float if the currency symbol is not included.
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
 * Gets the initials from a given name.
 *
 * If the given name contains two words, the function returns the first letter
 * of each word in uppercase. If the given name contains only one word, the
 * function returns the first letter of the word in uppercase.
 *
 * @param string $name The name to get the initials from.
 * @return string The initials of the given name.
 */
function getNameInitials(string $name): string
{
    $words = explode(' ', $name);

    if (count($words) > 1) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }

    return strtoupper(substr($words[0], 0, 1));
}
