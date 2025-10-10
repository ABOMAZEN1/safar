<?php

namespace App\Jobs;

use App\Services\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // اجعلها public لكي تُسلسَل وتُعاد بشكل سليم
    public array $tokens;
    public string $title;
    public string $body;

    public function __construct(array $tokens, string $title, string $body)
    {
        $this->tokens = $tokens;
        $this->title = $title;
        $this->body = $body;
    }

    public function handle(): void
    {
        try {
            Log::info('SendPushBatch START', ['count' => count($this->tokens ?? []), 'title' => $this->title]);

            // فحص أمان؛ إن لم تكن مصفوفة نوقف التنفيذ بعد تسجيل سبب المشكلة
            if (!is_array($this->tokens) || empty($this->tokens)) {
                Log::warning('SendPushBatch: tokens empty or not an array', [
                    'tokens_type' => gettype($this->tokens),
                    'tokens_sample' => $this->tokens,
                ]);
                return;
            }

            foreach ($this->tokens as $token) {
                try {
                    $res = FirebaseService::send($token, $this->title, $this->body);

                    Log::info("FCM Send Success", [
                        'token' => $token,
                        'title' => $this->title,
                        'body' => $this->body,
                        'response' => $res,
                    ]);
                } catch (\Throwable $e) {
                    Log::error("FCM Send Failed: " . $e->getMessage(), [
                        'token' => $token,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            Log::info('SendPushBatch END', ['count' => count($this->tokens)]);
        } catch (\Throwable $t) {
            Log::error('SendPushBatch fatal error', [
                'message' => $t->getMessage(),
                'trace' => $t->getTraceAsString(),
            ]);
            // فشل واضح: نعلم النظام أن الـ job فشل نهائياً (اختياري)
            $this->fail($t);
        }
    }
}
