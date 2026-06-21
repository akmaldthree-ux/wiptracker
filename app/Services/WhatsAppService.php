<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $token;
    private bool   $enabled;

    public function __construct()
    {
        $this->token   = config('services.fonnte.token', '');
        $this->enabled = config('services.fonnte.enabled', false) && !empty($this->token);
    }

    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled) return false;

        // Normalize phone: strip leading 0 → 62xxx
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->timeout(8)
                ->post('https://api.fonnte.com/send', [
                    'target'  => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            if (!$response->successful()) {
                Log::warning('WhatsApp send failed', ['phone' => $phone, 'response' => $response->body()]);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function sendToUser(\App\Models\User $user, string $message): bool
    {
        if (empty($user->phone)) return false;
        return $this->send($user->phone, $message);
    }

    public function isEnabled(): bool { return $this->enabled; }
}
