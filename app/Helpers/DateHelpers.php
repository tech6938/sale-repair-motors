<?php

use Carbon\Carbon;

/**
 * Convert a datetime string into a human-readable "time ago" format.
 */
function humanTime(string $datetime): string
{
    return Carbon::parse($datetime)->diffForHumans();
}

/**
 * Convert a given date to a database-friendly format (e.g., '2024-11-18').
 * If no date is provided, the current date is returned.
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
 */
function dbDateTime(?string $datetime = null): string
{
    return !empty($datetime)
        ? Carbon::parse($datetime)->format('Y-m-d H:i:s')
        : Carbon::now()->format('Y-m-d H:i:s');
}

/**
 * Format a given date for frontend display.
 * If no date is provided, the current date is returned.
 * Allows customization of the output format.
 */
function frontendDate(?string $date = null, ?string $format = 'Y-m-d'): string
{
    return !empty($date)
        ? Carbon::parse($date)->format($format)
        : Carbon::now()->format($format);
}

/**
 * Format a given datetime for frontend display.
 * If no datetime is provided, the current datetime is used.
 * Allows customization of the output format.
 */
function frontendDateTime(?string $datetime = null, string $format = 'Y-m-d h:i A'): string
{
    return !empty($datetime)
        ? Carbon::parse($datetime)->format($format)
        : Carbon::now()->format($format);
}
