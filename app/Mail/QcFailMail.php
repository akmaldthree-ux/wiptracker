<?php
namespace App\Mail;
use App\Models\QcInspection;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QcFailMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public QcInspection $inspection, public User $recipient) {}
    public function build(): static {
        return $this->markdown('mail.qc-fail')
            ->subject("🚨 QC Gagal: {$this->inspection->order->order_no} — Defect {$this->inspection->defect_rate}%");
    }
}
