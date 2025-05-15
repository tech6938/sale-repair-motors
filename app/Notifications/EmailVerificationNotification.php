<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification())
            ]
        );

        $role = $notifiable?->roles()->first()?->name;

        return (new MailMessage)
            ->view('emails.notification', [
                'subject' => 'Email Verification Notification',
                'name' => $notifiable?->name,
                'content' => 'Thank you for registering on ' . config('app.name') . ' as a <i>' . humanize($role) . '</i>. Just one more step before you can start using our platform.',
                'meta' => [
                    'heading' => 'Welcome to ' . config('app.name') . '!',
                    'note' => 'Please, click the button below to verify your email address. The invitation link will expire after 24 hours.',
                    'url' => $verifyUrl,
                    'button_text' => 'Verify Email',
                ]
            ])
            ->subject('Email Verification Notification');
    }
}
