<?php
namespace App\Console\Commands;
use App\Mail\HandoverPendingMail;
use App\Models\{Handover, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckPendingHandovers extends Command
{
    protected $signature = 'check:pending-handovers';
    protected $description = 'Check and notify about handovers pending for more than 24 hours';

    public function handle(): void
    {
        $pendingHandovers = Handover::with(['order','fromStation'])
            ->where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($pendingHandovers->isEmpty()) {
            $this->info('No pending handovers older than 24 hours.');
            return;
        }

        $recipients = User::whereIn('role', ['admin', 'supervisor'])->get();

        foreach ($recipients as $user) {
            if ($user->email) {
                Mail::to($user->email)->send(new HandoverPendingMail($pendingHandovers));
            }
        }

        $count = $pendingHandovers->count();
        Log::info("CheckPendingHandovers: Sent alert for {$count} pending handovers to " . $recipients->count() . " recipients.");
        $this->info("Alert sent for {$count} pending handovers.");
    }
}
