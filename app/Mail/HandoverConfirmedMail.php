<?php
namespace App\Mail;
use App\Models\Handover;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HandoverConfirmedMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public Handover $handover, public User $recipient) {}
    public function build(): static {
        $subject = $this->handover->status === 'discrepancy'
            ? "⚠ Discrepancy Handover: {$this->handover->handover_no}"
            : "✓ Handover Dikonfirmasi: {$this->handover->handover_no}";
        return $this->markdown('mail.handover-confirmed')->subject($subject);
    }
}
