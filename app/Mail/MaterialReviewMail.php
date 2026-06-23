<?php
namespace App\Mail;

use App\Models\ProductionOrder;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaterialReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProductionOrder $order,
        public User $recipient
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[DPIS] Review Bahan Baku — {$this->order->order_no}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.material-review');
    }
}
