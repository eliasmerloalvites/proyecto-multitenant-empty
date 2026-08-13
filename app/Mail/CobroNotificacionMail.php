<?php

namespace App\Mail;

use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CobroNotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $tipo  'recordatorio' | 'vencido' | 'suspension'
     */
    public function __construct(
        public Client $client,
        public string $tipo,
        public Carbon $fechaCobro,
        public float $monto,
    ) {
    }

    public function build()
    {
        $asuntos = [
            'recordatorio' => 'Tu próximo pago vence pronto — Kael Tech',
            'vencido' => 'Tu pago está vencido — Kael Tech',
            'suspension' => 'Tu cuenta fue suspendida por falta de pago — Kael Tech',
        ];

        return $this->subject($asuntos[$this->tipo])
            ->view('emails.cobro-notificacion');
    }
}
