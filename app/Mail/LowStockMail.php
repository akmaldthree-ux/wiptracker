<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public $materials) {}
    public function build(): static {
        return $this->markdown('mail.low-stock')
            ->subject("⚠ Stok Kritis: {$this->materials->count()} Bahan Baku Di Bawah Minimum");
    }
}
