<?php

namespace App\Jobs;

use App\Models\BusTripBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQrCodeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    /**
     * QR code styling constants
     */
    private const QR_STORAGE_DIRECTORY = 'qrs';

    private const QR_FILE_PREFIX = 'booking-qr-';

    private const QR_FILE_EXTENSION = '.svg';

    /**
     * Create a new job instance.
     */
    public function __construct(
        private BusTripBooking $busTripBooking
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = $this->generateAndSaveQrCode();
        $this->busTripBooking->update(['qr_code_path' => $filePath]);
    }

    /**
     * Generate and save QR code for the booking.
     */
    private function generateAndSaveQrCode(): string
    {
        $fileName = sprintf('%s%s%s', self::QR_FILE_PREFIX, $this->busTripBooking->id, self::QR_FILE_EXTENSION);
        $filePath = self::QR_STORAGE_DIRECTORY . '/' . $fileName;

        $this->ensureStorageDirectoryExists();

        $payload = [
            'id' => $this->busTripBooking->id,
            'customer' => $this->busTripBooking->customer_id,
            'seats' => $this->busTripBooking->reserved_seat_numbers,
            'trip' => $this->busTripBooking->bus_trip_id,
            'time' => now()->timestamp,
        ];

        $signature = hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), (string) config('app.key'));
        $payload['signature'] = $signature;

        $qrContent = json_encode($payload, JSON_THROW_ON_ERROR);

        $qrCode = QrCode::generate($qrContent);
        $fullPath = storage_path('app/public/' . $filePath);
        file_put_contents($fullPath, $qrCode);

        return $filePath;
    }

    /**
     * Ensure the storage directory for QR codes exists.
     */
    private function ensureStorageDirectoryExists(): void
    {
        if (! Storage::disk('public')->exists(self::QR_STORAGE_DIRECTORY)) {
            Storage::disk('public')->makeDirectory(self::QR_STORAGE_DIRECTORY);
        }
    }
}
