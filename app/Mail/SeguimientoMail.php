<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Correo de seguimiento comercial: el texto ya viene redactado (plantilla
 * con variables resueltas, posiblemente editado a mano por el staff).
 */
class SeguimientoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $asunto,
        public string $cuerpo,
        public ?string $urlBoton = null,
        public ?string $textoBoton = null,
    ) {
    }

    public function build()
    {
        return $this->subject($this->asunto)->view('emails.seguimiento');
    }
}
