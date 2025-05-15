<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OtpEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private object $otp) {}

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
        $role = $notifiable?->roles()->first()?->name;

        return (new MailMessage)
            ->view('emails.notification', [
                'subject' => 'Email OTP Notification',
                'name' => $notifiable?->name,
                'content' => 'Thank you for being part of ' . config('app.name') . ' as a <i>' . humanize($role) . '</i>. It looks like you have requested an OTP.
                    <br><br> Please find your one time password (OTP) below:',
                'meta' => [
                    'heading' => 'Email OTP Notification',
                    'otp' => $this->otp->token,
                    'note' => 'The token will expire in ' . config('otp.validity') . ' minutes. If you did not request this token then please ignore this email.',
                ],
            ])
            ->subject('Email OTP Notification');
    }
}
