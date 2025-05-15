<?php

/**
 * Return HTML indicating that the given value is unavailable if it is empty,
 * otherwise return the value itself.
 */
function canEmpty(mixed $value = null): string
{
    return empty($value) ? '<small><i>Unavailable</i></small>' : $value;
}

/**
 * Returns a random CSS class name for background color styling.
 */
function getRandomColorClass(): string
{
    $classes = ['bg-warning', 'bg-info', 'bg-danger', 'bg-blue', 'bg-pink', 'bg-indigo', 'bg-secondary', 'bg-purple'];

    return $classes[mt_rand(0, count($classes) - 1)];
}

/**
 * Returns HTML for displaying the user's avatar.
 */
function getAvatarHtml(null|App\Models\User $user = null): string
{
    $user = !empty($user) ? $user : auth()->user();

    if (
        !empty($user->avatar)
        && Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)
    ) {
        return '
            <span style="display: none;">' . getNameInitials($user->name) . '</span>
            <img src="' . getImageUrlByPath($user->avatar, true) . '" alt="avatar" >
        ';
    }

    return '
        <span>' . getNameInitials($user->name) . '</span>
        <img src="" alt="avatar" style="display: none;">
    ';
}
