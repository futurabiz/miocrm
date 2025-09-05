<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkflowActionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Le proprietà pubbliche saranno automaticamente disponibili nella vista dell'email.
     */
    public $emailSubject;
    public $emailBody;

    /**
     * Crea una nuova istanza del messaggio.
     *
     * @param string $subject L'oggetto dell'email.
     * @param string $body Il corpo del messaggio.
     */
    public function __construct(string $subject, string $body)
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
    }

    /**
     * Definisce l'oggetto e altre intestazioni dell'email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Definisce il contenuto dell'email.
     * Stiamo usando una vista Markdown per un aspetto pulito e professionale.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.workflow.action',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
