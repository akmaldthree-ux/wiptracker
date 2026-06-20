<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HighRejectRateMail extends Mailable {
    use Queueable, SerializesModels;
    public $rejectRate;
    public $month;
    public function __construct($rejectRate, $month) {
        $this->rejectRate = $rejectRate;
        $this->month = $month;
    }
    public function build() {
        return $this->markdown('mail.high-reject-rate')
            ->subject('Alert: Tingkat Reject Tinggi - ' . $this->month);
    }
}
