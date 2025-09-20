<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSchedulingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $scheduling;

    public function __construct($scheduling)
    {
        $this->scheduling = $scheduling;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Novo agendamento')
                    ->greeting('Olá, você tem um novo agendamento!') 
                    ->line('Data início: ' . $this->scheduling->start_date)
                    ->line('Data fim: ' . $this->scheduling->end_date)
                    ->line('Cliente ID: ' . $this->scheduling->client_id)
                    ->salutation('Barbearia');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'scheduling_id' => $this->scheduling?->id,
            'start_date' => $this->scheduling?->start_date,
            'end_date' => $this->scheduling?->end_date,
            'client_id' => $this->scheduling?->client_id,
        ];
    }
}
