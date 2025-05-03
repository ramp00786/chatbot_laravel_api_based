<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key_id',
        'user_email',
        'user_name',
        'user_mobile',
        'ip_address',
        'user_agent',
        'device_type',
        'location',
        'started_at',
        'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];


    // Define the relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Get the API key associated with this session
     */
    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(ApiKey::class);
    }

    /**
     * Get all messages for this session
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatLog::class, 'session_id', 'id');
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Scope for completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * End the current session
     */
    public function endSession(): void
    {
        $this->update(['ended_at' => now()]);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return is_null($this->ended_at);
    }
}