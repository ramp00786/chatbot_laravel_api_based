<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'name',
        'is_active',
        'last_used_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatSessions()
    {
        return $this->hasMany(ChatSession::class);
    }
}