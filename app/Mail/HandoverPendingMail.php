<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HandoverPendingMail extends Mailable {
    use Queueable, SerializesModels;
    public $handovers;
    public function __construct($handovers) { $this->handovers = $handovers; }
    public function build() {
        return $this->markdown('mail.handover-pending')
            ->subject('Alert: Handover Pending > 24 Jam');
    }
}
