<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReworkCreatedMail extends Mailable {
    use Queueable, SerializesModels;
    public $rework;
    public function __construct($rework) { $this->rework = $rework; }
    public function build() {
        return $this->markdown('mail.rework-created')
            ->subject('Info: Rework Order Dibuat');
    }
}
