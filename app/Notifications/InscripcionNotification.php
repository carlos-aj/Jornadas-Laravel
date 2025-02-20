<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inscripcion;
use App\Models\Eventos;

class InscripcionNotification extends Notification
{
    use Queueable;

    protected $inscripcion;
    protected $evento;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
        $this->evento = Eventos::find($inscripcion->evento_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirmación de Inscripción')
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('Te has inscrito exitosamente en el evento: ' . $this->evento->titulo)
            ->line('Tipo de inscripción: ' . $this->inscripcion->tipo_inscripcion)
            ->line('Fecha: ' . $this->evento->fecha)
            ->line('Hora: ' . $this->evento->hora)
            ->line('Ponente: ' . ($this->evento->ponente ? $this->evento->ponente->nombre : 'No asignado'))
            ->line('Gracias por inscribirte en nuestro evento.')
            ->line('¡Nos vemos pronto!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'evento_id' => $this->evento->id,
            'titulo' => $this->evento->titulo,
            'tipo_inscripcion' => $this->inscripcion->tipo_inscripcion,
            'fecha' => $this->evento->fecha,
            'hora' => $this->evento->hora,
        ];
    }
}