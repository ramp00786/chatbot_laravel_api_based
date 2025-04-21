<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use App\Models\ChatLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {

        // DB::table('chat_logs')->delete();

        $user = auth()->user();
        
        // Basic Stats
        $stats = [
            'total_sessions' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            
            'active_sessions' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereNull('ended_at')->count(),
            
            'total_messages' => ChatMessage::whereHas('chatSession.apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            
            'active_api_keys' => $user->apiKeys()->where('is_active', true)->count()
        ];

        // Location Data
        $locations = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Device Data
        $devices = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->orderBy('total', 'desc')
            ->get();

        // Recent Sessions
        $recentSessions = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with('apiKey')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'locations', 'devices', 'recentSessions'));
    }

    // public function showSessionHistory($session_id)
    // {
    //     $logs = DB::table('chat_logs')
    //         ->where('session_id', $session_id)
    //         ->orderBy('created_at')
    //         ->get();

    //     // dd($logs);
    //     return view('admin.chat_session', compact('logs'));
    // }

    public function showSessionHistory($session_id)
    {
        $logs = ChatLog::where('session_id', $session_id)
            ->orderBy('created_at')
            ->get();

        $session = ChatSession::with('user') // assuming you have a relationship
            ->findOrFail($session_id);

        return view('admin.chat_session', compact('logs', 'session'));
    }
}