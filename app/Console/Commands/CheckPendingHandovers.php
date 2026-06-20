<?php
namespace App\Console\Commands;

use App\Mail\HandoverPendingMail;
use App\Models\{Handover, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckPendingHandovers extends Command
{
    protected $signature   = 'check:pending-handovers';
    protected $description = 'Check for handovers pending > 24 hours and notify admins/supervisors';

    public function handle(): void
    {
        $pending = Handover::where('status', 'pending')
            ->where('initiated_at', '<', now()->subHours(24))
            ->with(['initiatedBy', 'fromStation', 'toStation', 'order'])
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No overdue pending handovers found.');
            return;
        }

        $recipients = User::whereIn('role', ['admin', 'supervisor'])
            ->whereNotNull('email')
            ->pluck('email');

        foreach ($pending as $handover) {
            foreach ($recipients as $email) {
                Mail::to($email)->send(new HandoverPendingMail($handover));
            }
        }

        $this->info("Notified {$recipients->count()} recipients about {$pending->count()} overdue handovers.");
    }
}
