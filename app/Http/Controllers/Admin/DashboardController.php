<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ApiKey;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
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
}