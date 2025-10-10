<?php

// namespace App\Jobs;

// use App\Models\AppNotification;
// use App\Models\User;
// use App\Services\FirebaseService;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;

// class SendNotificationJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public $timeout = 120;
//     public $tries = 3;

//     /**
//      * Create a new job instance.
//      */
//     public function __construct(
//         public AppNotification $notification
//     ) {}

//     /**
//      * Execute the job.
//      */
//     public function handle(FirebaseService $firebaseService): void
//     {
//         try {
//             Log::info("Starting to send notification: {$this->notification->id}");

//             // Update notification status to sending
//             $this->notification->update(['status' => 'sending']);

//             // Prepare payload
//             $payload = $firebaseService->buildPayload(
//                 title: $this->notification->title,
//                 body: $this->notification->body,
//                 image: $this->notification->image_url,
//                 data: $this->notification->data ?? [],
//                 clickAction: $this->notification->click_action
//             );

//             $result = $this->sendNotification($firebaseService, $payload);

//             if ($result['success']) {
//                 $this->notification->update([
//                     'status' => 'sent',
//                     'sent_at' => now()
//                 ]);
//                 Log::info("Notification sent successfully: {$this->notification->id}");
//             } else {
//                 throw new \Exception($result['message']);
//             }

//         } catch (\Exception $e) {
//             Log::error("Failed to send notification {$this->notification->id}: " . $e->getMessage());
            
//             $this->notification->update([
//                 'status' => 'failed',
//                 'data' => array_merge(
//                     $this->notification->data ?? [],
//                     ['error' => $e->getMessage(), 'failed_at' => now()]
//                 )
//             ]);

//             // Re-throw to trigger retry mechanism
//             throw $e;
//         }
//     }

//     /**
//      * Send notification based on target type
//      */
//     private function sendNotification(FirebaseService $firebaseService, array $payload): array
//     {
//         return match ($this->notification->target_type) {
//             'all' => $firebaseService->sendToAll($payload),
//             'specific' => $this->sendToSpecificUsers($firebaseService, $payload),
//             'segment' => $this->sendToSegment($firebaseService, $payload),
//             default => throw new \Exception('Invalid target type')
//         };
//     }

//     /**
//      * Send to specific users
//      */
//     private function sendToSpecificUsers(FirebaseService $firebaseService, array $payload): array
//     {
//         if (empty($this->notification->target_ids)) {
//             throw new \Exception('No target users specified for specific notification');
//         }

//         // Get Firebase tokens for the specific users
//         $tokens = User::whereIn('id', $this->notification->target_ids)
//             ->whereNotNull('firebase_token')
//             ->pluck('firebase_token')
//             ->toArray();

//         if (empty($tokens)) {
//             throw new \Exception('No valid Firebase tokens found for target users');
//         }

//         return $firebaseService->sendToTokens($tokens, $payload);
//     }

//     /**
//      * Send to user segment based on criteria
//      */
//     private function sendToSegment(FirebaseService $firebaseService, array $payload): array
//     {
//         if (!$this->notification->target_ids) {
//             throw new \Exception('No segment criteria specified');
//         }

//         $criteria = $this->notification->target_ids;
        
//         $query = User::whereNotNull('firebase_token');

//         // Apply segment criteria
//         foreach ($criteria as $key => $value) {
//             match ($key) {
//                 'city_id' => $query->where('city_id', $value),
//                 'age_min' => $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?', [$value]),
//                 'age_max' => $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?', [$value]),
//                 'registration_date_from' => $query->whereDate('created_at', '>=', $value),
//                 'registration_date_to' => $query->whereDate('created_at', '<=', $value),
//                 default => null
//             };
//         }

//         $tokens = $query->pluck('firebase_token')->toArray();

//         if (empty($tokens)) {
//             throw new \Exception('No users found matching segment criteria');
//         }

//         return $firebaseService->sendToTokens($tokens, $payload);
//     }

//     /**
//      * Handle a job failure.
//      */
//     public function failed(\Throwable $exception): void
//     {
//         Log::error("Notification job failed permanently: {$this->notification->id}", [
//             'exception' => $exception->getMessage(),
//             'trace' => $exception->getTraceAsString()
//         ]);

//         $this->notification->update([
//             'status' => 'failed',
//             'data' => array_merge(
//                 $this->notification->data ?? [],
//                 [
//                     'error' => $exception->getMessage(),
//                     'failed_at' => now(),
//                     'retry_count' => $this->attempts()
//                 ]
//             )
//         ]);
//     }
// }
