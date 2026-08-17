<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombre,
        public string $urlReset,
    ) {
    }

    public function build()
    {
        return $this->subject('Recupera tu contraseña — Kael Tech')
            ->view('emails.reset-password');
    }
}
