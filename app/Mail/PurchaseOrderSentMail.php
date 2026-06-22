<?php
namespace App\Mail;
use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderSentMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public PurchaseOrder $po) {}
    public function build(): static {
        return $this->markdown('mail.purchase-order-sent')
            ->subject("Purchase Order {$this->po->po_no} dari DTHREE Production");
    }
}
