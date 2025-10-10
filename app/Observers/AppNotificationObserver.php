<?php

namespace App\Observers;

use App\Jobs\SendNotificationJob;
use App\Models\AppNotification;
use Illuminate\Support\Facades\DB;

class AppNotificationObserver
{
    /**
     * Handle the AppNotification "created" event.
     */
    public function created(AppNotification $notification): void
    {
        // Auto-send notification if status is draft and no schedule time
        if ($notification->status === 'draft' && !$notification->scheduled_at) {
            dispatch(new SendNotificationJob($notification));
        }
    }

    /**
     * Handle the AppNotification "updated" event.
     */
    public function updated(AppNotification $notification): void
    {
        // Check if status was changed to draft for immediate sending
        if ($notification->wasChanged('status') && $notification->status === 'draft') {
            dispatch(new SendNotificationJob($notification));
        }

        // Check if scheduled_at was updated and status remains scheduled
        if ($notification->wasChanged('scheduled_at') && 
            $notification->status === 'scheduled' && 
            $notification->scheduled_at && 
            $notification->scheduled_at->isFuture()) {
            
            // Update the delayed job for the scheduled notification
            dispatch(new SendNotificationJob($notification))
                ->delay($notification->scheduled_at->subSecond(30));
        }
    }
}
