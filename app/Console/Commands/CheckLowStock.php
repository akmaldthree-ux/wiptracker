<?php
namespace App\Console\Commands;
use App\Mail\LowStockMail;
use App\Models\{RawMaterial, User};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class CheckLowStock extends Command
{
    protected $signature = 'check:low-stock';
    protected $description = 'Check raw materials below minimum stock and notify warehouse staff';

    public function handle(): void
    {
        $lowStock = RawMaterial::where('is_active', true)
            ->whereRaw('current_stock < min_stock')
            ->get();

        if ($lowStock->isEmpty()) {
            $this->info('All stock levels are sufficient.');
            return;
        }

        $recipients = User::where(function ($q) {
            $q->whereIn('role', ['admin', 'supervisor'])
              ->orWhere('role', 'staff_gudang');
        })->whereNotNull('email')->get();

        foreach ($recipients as $user) {
            try {
                Mail::to($user->email)->send(new LowStockMail($lowStock));
            } catch (\Throwable $e) {
                Log::error('LowStockMail failed', ['to' => $user->email, 'error' => $e->getMessage()]);
            }
        }

        Log::info("CheckLowStock: {$lowStock->count()} items below minimum — alert sent to {$recipients->count()} recipients.");
        $this->info("Alert sent for {$lowStock->count()} low stock items.");
    }
}
