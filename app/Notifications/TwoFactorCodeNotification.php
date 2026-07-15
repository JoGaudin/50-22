<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de connexion')
            ->greeting('Bonjour,')
            ->line('Voici votre code de vérification pour finaliser votre connexion :')
            ->line('**'.$this->code.'**')
            ->line('Ce code est valable **5 minutes**.')
            ->line('Si vous n\'avez pas demandé ce code, ignorez cet e-mail.')
            ->salutation('Cordialement,');
    }
}
