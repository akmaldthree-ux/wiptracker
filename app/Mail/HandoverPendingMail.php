<?php
namespace App\Mail;

use App\Models\Handover;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HandoverPendingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Handover $handover) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[DPIS] Handover Pending Terlalu Lama — ' . $this->handover->handover_no);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.handover-pending');
    }
}
