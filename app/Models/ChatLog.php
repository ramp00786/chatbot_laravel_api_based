<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'sender',
        'type',
        'message',
        'parent_id'
    ];

    public function session()
    {
        return $this->belongsTo(ChatSession::class, 'session_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(ChatLog::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ChatLog::class, 'parent_id');
    }
}