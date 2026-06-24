<?php
namespace App\Mail;

use App\Models\Handover;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReworkCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Handover $rework) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[DPIS] Rework Masuk — {$this->rework->handover_no}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.rework-created');
    }
}
