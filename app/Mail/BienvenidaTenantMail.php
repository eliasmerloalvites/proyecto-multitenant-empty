<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BienvenidaTenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $razonSocial,
        public string $urlLogin,
        public string $email,
        public string $planNombre,
    ) {
    }

    public function build()
    {
        return $this->subject('¡Bienvenido a Kael Tech! Tu taller ya está listo 🎉')
            ->view('emails.bienvenida-tenant');
    }
}
