<?php

/**
 * Returns the given value if it is not empty, otherwise returns a string
 * indicating that the value is unavailable.
 *
 * @param mixed $value The value to check.
 * @return string The value, or a string indicating that the value is unavailable.
 */
function canEmpty(mixed $value = null): string
{
    return empty($value) ? '<small><i>Unavailable</i></small>' : $value;
}

/**
 * Returns a random Bootstrap color class.
 *
 * @return string A random Bootstrap color class.
 */
function getRandomColorClass(): string
{
    $classes = ['bg-warning', 'bg-info', 'bg-danger', 'bg-blue', 'bg-pink', 'bg-indigo', 'bg-secondary', 'bg-purple'];

    return $classes[mt_rand(0, count($classes) - 1)];
}

/**
 * Returns an HTML string containing the avatar for the given user.
 *
 * If the user has an avatar, the avatar image will be displayed.
 * If the user does not have an avatar, the first letter of the user's name
 * will be used to generate an avatar.
 *
 * @param null|App\Models\User $user The user for which to generate the avatar.
 * @return string The HTML string containing the avatar for the given user.
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
