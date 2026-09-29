<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $loginUrl, public string $motivo = '')
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cadastro não aprovado - Alphaview Serviços Residenciais',
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.user-rejected',
        );
    }
}
