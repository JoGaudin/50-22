<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = URL::temporarySignedRoute(
            'invitation.accept.show',
            now()->addDays(7),
            ['user' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject(__('Invitation à rejoindre :app', ['app' => config('app.name')]))
            ->line(__('Un administrateur vous a créé un compte. Définissez votre mot de passe pour activer l’accès.'))
            ->action(__('Définir mon mot de passe'), $acceptUrl);
    }
}
