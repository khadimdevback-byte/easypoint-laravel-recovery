<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;
    public $isCopy;

    public function __construct(Contact $contact, $isCopy = false)
    {
        $this->contact = $contact;
        $this->isCopy = $isCopy;
    }

    public function envelope(): Envelope
    {
        if ($this->isCopy) {
            return new Envelope(
                subject: 'Confirmation : Votre message a bien été reçu - EasyPoint',
            );
        }

        return new Envelope(
            subject: 'Nouveau message de contact - ' . $this->contact->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
        );
    }
}
