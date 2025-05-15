<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $token) {}

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
        return (new MailMessage)
            ->view('emails.notification', [
                'subject' => 'Reset Password Notification',
                'name' => $notifiable?->name,
                'content' => 'You are receiving this email because we received a password reset request for your account.',
                'meta' => [
                    'heading' => 'Reset password to ' . config('app.name'),
                    'note' => 'This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.',
                    'url' => route('password.reset', [
                        'token' => $this->token,
                        'email' => $notifiable->getEmailForPasswordReset(),
                    ]),
                    'button_text' => 'Reset Password',
                ]
            ])
            ->subject('Reset Password Notification');
    }
}
