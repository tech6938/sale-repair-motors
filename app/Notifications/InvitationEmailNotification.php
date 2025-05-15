<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Password;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvitationEmailNotification extends Notification implements ShouldQueue
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
        $role = $notifiable?->roles()->first()?->name;

        return (new MailMessage)
            ->view('emails.notification', [
                'subject' => 'Invitation to ' . config('app.name'),
                'name' => $notifiable?->name,
                'content' => 'You have been invited to <b>' . config('app.name') . '</b> as a <i>' . humanize($role) . '</i>. <br>Please, use the following link to accept the invitation.',
                'meta' => [
                    'heading' => 'Invitation to ' . config('app.name'),
                    'note' => 'If you did not request an account, no further action is required. The invitation link will expire after 24 hours.',
                    'url' => route('password.reset', [
                        'token' => Password::createToken($notifiable),
                        'email' => $notifiable->email,
                    ]),
                    'button_text' => 'Accept Invitation',
                ]
            ])
            ->subject('Invitation to ' . config('app.name'));
    }
}
