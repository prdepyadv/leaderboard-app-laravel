<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SaveUserAddressInQR implements ShouldQueue
{
    use Queueable;

    private $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            $this->saveQRCode();
        } catch (\Exception $e) {
            Log::error('Error saving QR code: ' . $e->getMessage());
        }

    }

    private function saveQRCode(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::error('User not found with UserId: ' . $this->userId);
            return;
        }

        $userAddress = $user->address;
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($userAddress);
        $response = Http::get($qrCodeUrl);

        if ($response->successful()) {
            $fileName = 'qrcodes/user_' . $this->userId . '_' . now()->timestamp . '.png';
            Storage::put($fileName, $response->body());
        } else {
            Log::error('Error generating QR code: ' . $response->body());
        }
    }
}
