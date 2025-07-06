<?php

use Carbon\Carbon;

/**
 * Format a given datetime as a relative time from the current time.
 * E.g., '3 hours ago', '2 days from now', etc.
 * If no datetime is provided, the current datetime is used.
 *
 * @param string $datetime The datetime to format.
 * @return string The formatted datetime.
 */
function humanTime(string $datetime): string
{
    return Carbon::parse($datetime)->diffForHumans();
}

/**
 * Convert a given datetime to a database-friendly format (e.g., '2024-11-18 12:34:56').
 * If no datetime is provided, the current datetime is returned.
 *
 * @param string|null $date The datetime to format.
 * @return string The formatted datetime in the format 'Y-m-d'.
 */
function dbDate(?string $date = null): string
{
    return !empty($date)
        ? Carbon::parse($date)->format('Y-m-d')
        : Carbon::now()->format('Y-m-d');
}

/**
 * Convert a given datetime to a database-friendly format (e.g., '2024-11-18 12:34:56').
 * If no datetime is provided, the current datetime is returned.
 *
 * @param string|null $datetime The datetime to format.
 * @return string The formatted datetime in the format 'Y-m-d H:i:s'.
 */
function dbDateTime(?string $datetime = null): string
{
    return !empty($datetime)
        ? Carbon::parse($datetime)->format('Y-m-d H:i:s')
        : Carbon::now()->format('Y-m-d H:i:s');
}

/**
 * Format a given date for frontend display.
 * If no date is provided, the current date is used.
 * Allows customization of the output format.
 *
 * @param string|null $date The date to format.
 * @param string|null $format The format to use. Defaults to 'd-M-Y'.
 * @return string The formatted date.
 */
function frontendDate(?string $date = null, ?string $format = 'd-M-Y'): string
{
    return !empty($date)
        ? Carbon::parse($date)->format($format)
        : '<small><i>Not Yet</i></small>';
}

/**
 * Format a given datetime for frontend display.
 * If no datetime is provided, returns a placeholder indicating absence.
 * Allows customization of the output format.
 *
 * @param string|null $datetime The datetime to format.
 * @param string $format The format to use. Defaults to 'd-M-Y h:i A'.
 * @return string The formatted datetime or a placeholder if none is provided.
 */
function frontendDateTime(?string $datetime = null, string $format = 'd-M-Y h:i A'): string
{
    return !empty($datetime)
        ? Carbon::parse($datetime)->format($format)
        : '<small><i>Not Yet</i></small>';
}
