<?php
namespace App\Console\Commands;

use App\Mail\HighRejectRateMail;
use App\Models\{HandoverItem, WipEntry, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckRejectRate extends Command
{
    protected $signature   = 'check:reject-rate';
    protected $description = 'Check monthly reject rate and notify if it exceeds 5%';

    public function handle(): void
    {
        $start = now()->startOfMonth();
        $end   = now()->endOfMonth();
        $month = now()->format('Y-m');

        $totalReject = HandoverItem::whereNotNull('reject_type')
            ->where('qty_reject', '>', 0)
            ->whereHas('handover', fn($q) => $q->whereIn('status', ['confirmed', 'approved', 'discrepancy'])
                ->whereBetween('confirmed_at', [$start, $end]))
            ->sum('qty_reject');

        $totalProduced = WipEntry::whereBetween('input_date', [$start->toDateString(), $end->toDateString()])
            ->sum('qty_out');

        if ($totalProduced == 0) {
            $this->info('No production data yet for this month.');
            return;
        }

        $rejectRate = round(($totalReject / $totalProduced) * 100, 2);

        if ($rejectRate <= 5) {
            $this->info("Reject rate {$rejectRate}% — within acceptable range.");
            return;
        }

        $recipients = User::whereIn('role', ['admin', 'supervisor'])
            ->whereNotNull('email')
            ->pluck('email');

        foreach ($recipients as $email) {
            Mail::to($email)->send(new HighRejectRateMail($rejectRate, (int) $totalReject, $month));
        }

        $this->info("Reject rate {$rejectRate}% exceeds 5%. Notified {$recipients->count()} recipients.");
    }
}
