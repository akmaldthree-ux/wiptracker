<?php
namespace App\Console\Commands;
use App\Mail\HighRejectRateMail;
use App\Models\{HandoverItem, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckRejectRate extends Command
{
    protected $signature = 'check:reject-rate';
    protected $description = 'Check monthly reject rate and notify if above threshold';

    public function handle(): void
    {
        $month = now()->format('F Y');

        $totalSent = HandoverItem::whereHas('handover', function($q) {
            $q->whereMonth('confirmed_at', now()->month)
              ->whereYear('confirmed_at', now()->year)
              ->whereNotNull('confirmed_at');
        })->sum('qty_sent');

        $totalReject = HandoverItem::whereHas('handover', function($q) {
            $q->whereMonth('confirmed_at', now()->month)
              ->whereYear('confirmed_at', now()->year)
              ->whereNotNull('confirmed_at');
        })->where('qty_reject', '>', 0)->sum('qty_reject');

        if ($totalSent == 0) {
            $this->info('No data to analyze.');
            return;
        }

        $rejectRate = ($totalReject / $totalSent) * 100;

        if ($rejectRate > 5) {
            $recipients = User::whereIn('role', ['admin', 'supervisor'])->get();
            foreach ($recipients as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new HighRejectRateMail($rejectRate, $month));
                }
            }
            Log::warning("CheckRejectRate: Reject rate {$rejectRate}% for {$month} — alerts sent.");
            $this->warn("Reject rate {$rejectRate}% exceeds threshold. Alerts sent.");
        } else {
            $this->info("Reject rate {$rejectRate}% is within acceptable range.");
        }
    }
}
