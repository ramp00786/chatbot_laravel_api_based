<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class StatisticController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sessions' => ChatSession::count(),
            'total_messages' => ChatMessage::count(),
            'active_sessions' => ChatSession::whereNull('ended_at')->count(),
        ];

        return view('admin.statistics.index', compact('stats'));
    }
}