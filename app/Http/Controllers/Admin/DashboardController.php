<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use App\Models\ChatLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $now = Carbon::now();
        $lastPeriod = Carbon::now()->subDays(30);
        
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
            
            'unique_locations' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->distinct('location')->count('location'),
            
            'desktop_users' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('device_type', 'Desktop')->count(),
            
            'todays_visitors' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereDate('created_at', $now)->count(),
        ];
        
        // Calculate percentage changes
        $previousStats = [
            'total_sessions' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereBetween('created_at', [$lastPeriod->copy()->subDays(30), $lastPeriod])->count(),
            
            'active_sessions' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereNull('ended_at')
              ->whereBetween('created_at', [$lastPeriod->copy()->subDays(30), $lastPeriod])
              ->count(),
            
            'total_messages' => ChatMessage::whereHas('chatSession.apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereBetween('created_at', [$lastPeriod->copy()->subDays(30), $lastPeriod])->count(),
            
            'unique_locations' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereBetween('created_at', [$lastPeriod->copy()->subDays(30), $lastPeriod])
              ->distinct('location')->count('location'),
            
            'desktop_users' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('device_type', 'Desktop')
              ->whereBetween('created_at', [$lastPeriod->copy()->subDays(30), $lastPeriod])
              ->count(),
            
            'todays_visitors' => ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereDate('created_at', $now->copy()->subDay())->count(),
        ];
        
        $changes = [];
        foreach ($stats as $key => $value) {
            $previous = $previousStats[$key] ?? 0;
            $changes[$key] = $previous != 0 ? (($value - $previous) / $previous) * 100 : 0;
        }

       
        
        // Sessions chart data (last 30 days)
        $sessionsChart = $this->generateTimeSeriesData('sessions', 30);
        $messagesChart = $this->generateTimeSeriesData('messages', 30);
        
        // Engagement chart data
        $engagementChart = $this->generateEngagementData(30);
        
        // Location Data
        $locations = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        $topLocations = $locations->map(function($item, $index) {
            $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
            return [
                'name' => $item->location ?? 'Unknown',
                'count' => $item->total,
                'color' => $colors[$index % count($colors)]
            ];
        });
        
        // Device Data
        $devices = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('device_type', DB::raw('count(*) as total'))
            ->groupBy('device_type')
            ->orderBy('total', 'desc')
            ->get();
        
        $deviceDistribution = [
            'Desktop' => $devices->firstWhere('device_type', 'Desktop')->total ?? 0,
            'Mobile' => $devices->firstWhere('device_type', 'Mobile')->total ?? 0,
            'Tablet' => $devices->firstWhere('device_type', 'Tablet')->total ?? 0,
        ];

        
        
        // Traffic sources (simulated data)
        $trafficSources = [
            'Direct' => rand(100, 500),
            'Referral' => rand(50, 300),
            'Social' => rand(30, 200),
            'Organic' => rand(200, 600),
        ];
        
        // Recent Sessions
        $recentSessions = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['apiKey', 'messages'])
            ->withCount('messages')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'stats',
            'changes',
            'sessionsChart',
            'messagesChart',
            'engagementChart',
            'topLocations',
            'deviceDistribution',
            'trafficSources',
            'recentSessions'
        ));
    }
    
    protected function generateTimeSeriesData($type, $days = 30)
    {
        $user = auth()->user();
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days);
        
        $results = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            
            if ($type === 'sessions') {
                $count = ChatSession::whereHas('apiKey', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->whereDate('created_at', $currentDate)
                    ->count();
            } else { // messages
                $count = ChatMessage::whereHas('chatSession.apiKey', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->whereDate('created_at', $currentDate)
                    ->count();
            }
            
            $results[$dateString] = $count;
            $currentDate->addDay();
        }
        
        return [
            'labels' => array_keys($results),
            'data' => array_values($results)
        ];
    }
    
    protected function generateEngagementData($days = 30)
    {
        $user = auth()->user();
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($days);
        
        $results = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('Y-m-d');
            
            $sessions = ChatSession::whereHas('apiKey', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereDate('created_at', $currentDate)
                ->get();
            
            $totalDuration = 0;
            $totalMessages = 0;
            $sessionCount = $sessions->count();
            
            foreach ($sessions as $session) {
                if ($session->ended_at) {
                    $totalDuration += $session->created_at->diffInSeconds($session->ended_at);
                }
                $totalMessages += $session->messages_count;
            }
            
            $avgDuration = $sessionCount > 0 ? $totalDuration / $sessionCount : 0;
            $avgMessages = $sessionCount > 0 ? $totalMessages / $sessionCount : 0;
            
            $results[$dateString] = [
                'duration' => round($avgDuration / 60, 2), // in minutes
                'messages' => round($avgMessages, 2)
            ];
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => array_keys($results),
            'duration' => array_column($results, 'duration'),
            'messages' => array_column($results, 'messages')
        ];
    }
    
    public function chartData($chart, $range)
    {
        $days = $range === 'week' ? 7 : ($range === 'month' ? 30 : 90);
        $data = [];
        
        if ($chart === 'sessionsChart') {
            $data = $this->generateTimeSeriesData('sessions', $days);
        } elseif ($chart === 'messagesChart') {
            $data = $this->generateTimeSeriesData('messages', $days);
        }
        
        return response()->json($data);
    }
    
    public function engagementData($range)
    {
        $days = $range === 'week' ? 7 : 30;
        $data = $this->generateEngagementData($days);
        
        return response()->json($data);
    }
    
    public function updateTimeRange($days)
    {
        $data = [
            'sessions' => $this->generateTimeSeriesData('sessions', $days),
            'messages' => $this->generateTimeSeriesData('messages', $days),
            // Add other chart data as needed
        ];
        
        return response()->json($data);
    }
    
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