<?php

namespace App\Notifications;

use App\Models\Inscripcion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionNotification extends Notification
{
    use Queueable;

    protected $inscripcion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $costo = $this->inscripcion->tipo === 'Presencial' ? '10 euros' : '5 euros';

        return (new MailMessage)
                    ->line('Te has inscrito exitosamente al evento.')
                    ->line('Evento: ' . $this->inscripcion->evento->titulo)
                    ->line('Tipo: ' . $this->inscripcion->tipo)
                    ->line('Costo: ' . $costo)
                    ->line('Fecha: ' . $this->inscripcion->evento->fecha)
                    ->line('Hora: ' . $this->inscripcion->evento->hora)
                    ->line('Gracias por inscribirte!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'evento_id' => $this->inscripcion->evento_id,
            'tipo' => $this->inscripcion->tipo,
        ];
    }
}
