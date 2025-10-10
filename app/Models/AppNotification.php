<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'body',
        'body_en',
        'image_url',
        'target_type', // 'all', 'specific', 'segment'
        'target_ids', // JSON array of user IDs or segment criteria
        'scheduled_at',
        'sent_at',
        'status', // 'draft', 'scheduled', 'sent', 'failed'
        'click_action',
        'data', // JSON data for custom payload
        'created_by',
    ];

    protected $casts = [
        'target_ids' => 'array',
        'data' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at;
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function isTargeted(): bool
    {
        return $this->target_type === 'specific';
    }

    public function isAllUsers(): bool
    {
        return $this->target_type === 'all';
    }
}
