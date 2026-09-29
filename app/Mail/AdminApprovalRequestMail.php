<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $approveUrl, public string $rejectUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo usuário aguardando aprovação - Alphaview',
        );
    }

    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.admin-approval-request',
        );
    }
}
