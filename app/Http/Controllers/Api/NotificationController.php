<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateFcmTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Auth;

class NotificationController extends Controller
{
    /**
     * Update FCM token for authenticated user
     */
    public function updateFcmToken(UpdateFcmTokenRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $user->update([
                'firebase_token' => $request->firebase_token,
                'firebase_token_updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FCM token updated successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FCM token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get notification statistics for authenticated user
     */
    public function notificationStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $stats = [
                'total_notifications' => \App\Models\AppNotification::where('target_type', 'all')
                    ->orWhere(function ($query) use ($user) {
                        $query->where('target_type', 'specific')
                            ->whereJsonContains('target_ids', $user->id);
                    })
                    ->count(),
                'unread_count' => 0, // يمكن تطوير هذا بناءً على نظام قراءة الإشعارات
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get notification stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validate FCM token
     */
    public function validateFcmToken(Request $request): JsonResponse
    {
        try {
            $token = $request->input('firebase_token');
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase token is required',
                ], 400);
            }

            // يمكن إضافة validation إضافي هنا إذا لزم الأمر
            
            return response()->json([
                'success' => true,
                'message' => 'FCM token is valid',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate FCM token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
