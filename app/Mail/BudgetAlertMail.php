<?php
namespace App\Mail;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetAlertMail extends Mailable {
    use Queueable, SerializesModels;
    public function __construct(public Budget $budget, public User $recipient, public string $alertType) {}
    public function build(): static {
        $subject = $this->alertType === 'over'
            ? "🚨 Over Budget: {$this->budget->order->order_no}"
            : "⚠ Budget Hampir Penuh: {$this->budget->order->order_no}";
        return $this->markdown('mail.budget-alert')->subject($subject);
    }
}
