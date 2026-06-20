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

    public function __construct(public Handover $reworkHandover, public Handover $sourceHandover) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[DPIS] Rework Handover Dibuat — ' . $this->reworkHandover->handover_no);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.rework-created');
    }
}
