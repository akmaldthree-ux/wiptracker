<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HighRejectRateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public float $rejectRate, public int $totalReject, public string $month) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[DPIS] Peringatan: Reject Rate Tinggi ' . $this->rejectRate . '% — ' . $this->month);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.high-reject-rate');
    }
}
