<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistroVerificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $razonSocial,
        public string $urlVerificacion,
    ) {
    }

    public function build()
    {
        return $this->subject('Confirma tu correo para activar tu taller — Kael Tech')
            ->view('emails.registro-verificacion');
    }
}
