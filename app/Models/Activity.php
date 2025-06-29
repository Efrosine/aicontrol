<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class Activity extends Model
{
    protected $fillable = [
        'type',
        'action',
        'title',
        'description',
        'status',
        'icon',
        'metadata',
        'user_id',
        'related_type',
        'related_id',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the user who triggered this activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model (polymorphic)
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->occurred_at->diffForHumans();
    }

    /**
     * Get badge class based on status
     */
    public function getBadgeClassAttribute(): string
    {
        return match($this->status) {
            'success' => 'badge-success',
            'warning' => 'badge-warning',
            'error' => 'badge-error',
            'info' => 'badge-info',
            default => 'badge-neutral',
        };
    }

    /**
     * Get badge text based on status and action
     */
    public function getBadgeTextAttribute(): string
    {
        return match($this->status) {
            'success' => 'SUCCESS',
            'warning' => 'ALERT',
            'error' => 'ERROR',
            'info' => match($this->action) {
                'completed' => 'NEW',
                'updated' => 'UPDATE',
                'created' => 'NEW',
                default => 'INFO',
            },
            default => strtoupper($this->action),
        };
    }

    /**
     * Create a new activity record
     */
    public static function log(
        string $type,
        string $action,
        string $title,
        string $description,
        string $status = 'info',
        ?User $user = null,
        ?Model $related = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'type' => $type,
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'user_id' => $user?->id ?? auth()->id(),
            'related_type' => $related ? get_class($related) : null,
            'related_id' => $related?->id,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }

    /**
     * Get recent activities for dashboard
     */
    public static function getRecent(int $limit = 10)
    {
        return self::with('user')
            ->orderBy('occurred_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Scope for filtering by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
