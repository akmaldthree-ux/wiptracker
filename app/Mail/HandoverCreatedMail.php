<?php
namespace App\Mail;

use App\Models\Handover;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HandoverCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Handover $handover, public User $recipient) {}

    public function build(): static
    {
        return $this->markdown('mail.handover-created')
            ->subject("Handover Masuk: {$this->handover->handover_no} — Perlu Konfirmasi");
    }
}
