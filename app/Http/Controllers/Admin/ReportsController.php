<?php

namespace App\Http\Controllers\Admin;

use App\Models\ApiKey;
use App\Models\ChatLog;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class ReportsController extends Controller
{
    // public function sessionsReport($type = 'all')
    // {
    //     $user = auth()->user();
    //     $query = ChatSession::whereHas('apiKey', function($q) use ($user) {
    //         $q->where('user_id', $user->id);
    //     });

    //     if ($type === 'active') {
    //         $query->whereNull('ended_at');
    //         $title = 'Active Sessions Report';
    //     } else {
    //         $title = 'All Sessions Report';
    //     }

    //     $sessions = $query->with(['messages', 'apiKey'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(25);

    //     return view('admin.reports.sessions', compact('sessions', 'title'));
    // }

    public function sessionsReport($type = 'all')
    {
        $user = auth()->user();
        $query = ChatSession::whereHas('apiKey', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        if ($type === 'active') {
            $query->whereNull('ended_at');
            $title = 'Active Sessions Report';
        } else {
            $title = 'All Sessions Report';
        }

        // Generate data for the timeline chart (last 30 days)
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);
        $last30Days = [];
        $sessionsCount = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('M d');
            $last30Days[] = $dateString;
            
            $count = $query->clone()
                ->whereDate('created_at', $currentDate)
                ->count();
                
            $sessionsCount[] = $count;
            $currentDate->addDay();
        }

        // SQLite-compatible duration calculation
        if (config('database.default') === 'sqlite') {
            // For SQLite
            $durationDistribution = [
                '<1 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw("(strftime('%s', ended_at) - strftime('%s', created_at)) < 60")
                    ->count(),
                '1-5 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw("(strftime('%s', ended_at) - strftime('%s', created_at)) BETWEEN 60 AND 300")
                    ->count(),
                '5-15 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw("(strftime('%s', ended_at) - strftime('%s', created_at)) BETWEEN 301 AND 900")
                    ->count(),
                '15-30 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw("(strftime('%s', ended_at) - strftime('%s', created_at)) BETWEEN 901 AND 1800")
                    ->count(),
                '30+ min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw("(strftime('%s', ended_at) - strftime('%s', created_at)) > 1800")
                    ->count(),
            ];
        } else {
            // For MySQL
            $durationDistribution = [
                '<1 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, ended_at) < 60')
                    ->count(),
                '1-5 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, ended_at) BETWEEN 60 AND 300')
                    ->count(),
                '5-15 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, ended_at) BETWEEN 301 AND 900')
                    ->count(),
                '15-30 min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, ended_at) BETWEEN 901 AND 1800')
                    ->count(),
                '30+ min' => $query->clone()
                    ->whereNotNull('ended_at')
                    ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, ended_at) > 1800')
                    ->count(),
            ];
        }

        $sessions = $query->with(['messages', 'apiKey'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.reports.sessions', compact(
            'sessions', 
            'title',
            'last30Days',
            'sessionsCount',
            'durationDistribution'
        ));
    }

    // public function messagesReport()
    // {
    //     $user = auth()->user();
    //     $messages = ChatMessage::whereHas('chatSession.apiKey', function($q) use ($user) {
    //             $q->where('user_id', $user->id);
    //         })
    //         ->with(['chatSession', 'chatSession.apiKey'])
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(50);

    //     return view('admin.reports.messages', compact('messages'));
    // }

    public function messagesReport()
    {
        $user = auth()->user();
        $query = ChatMessage::whereHas('chatSession.apiKey', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Apply filters if present
        $type = request()->get('type');
        if ($type === 'user') {
            $query->where('is_bot', false);
        } elseif ($type === 'bot') {
            $query->where('is_bot', true);
        }

        $dateRange = request()->get('date_range');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subWeek());
        } elseif ($dateRange === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subMonth());
        }

        // Get messages with pagination
        $messages = $query->with(['chatSession', 'chatSession.apiKey'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Prepare data for charts
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);
        $timelineLabels = [];
        $timelineData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('M d');
            $timelineLabels[] = $dateString;
            
            $count = $query->clone()
                ->whereDate('created_at', $currentDate)
                ->count();
                
            $timelineData[] = $count;
            $currentDate->addDay();
        }

        // Count message types
        $userMessagesCount = $query->clone()->where('is_bot', false)->count();
        $botMessagesCount = $query->clone()->where('is_bot', true)->count();

        return view('admin.reports.messages', compact(
            'messages',
            'timelineLabels',
            'timelineData',
            'userMessagesCount',
            'botMessagesCount'
        ));
    }

    public function locationsReport()
    {
        $user = auth()->user();
        $locations = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('location', DB::raw('count(*) as total'), 
                     DB::raw('avg(TIMESTAMPDIFF(SECOND, created_at, ended_at)) as avg_duration'),
                     DB::raw('count(distinct user_id) as unique_users'))
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->paginate(15);

        $worldData = $locations->map(function($item) {
            return [
                'name' => $item->location ?? 'Unknown',
                'value' => $item->total,
                'avg_duration' => $item->avg_duration
            ];
        });

        return view('admin.reports.locations', compact('locations', 'worldData'));
    }

    public function devicesReport()
    {
        $user = auth()->user();
        $devices = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('device_type', DB::raw('count(*) as total'),
                     DB::raw('avg(TIMESTAMPDIFF(SECOND, created_at, ended_at)) as avg_duration'))
            ->groupBy('device_type')
            ->orderBy('total', 'desc')
            ->get();

        return view('admin.reports.devices', compact('devices'));
    }

    public function analyticsReport()
    {
        $user = auth()->user();
        
        // Page analytics
        $pages = ChatLog::whereHas('chatSession.apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('page_url', DB::raw('count(*) as visits'),
                     DB::raw('avg(duration) as avg_duration'),
                     DB::raw('count(distinct session_id) as unique_sessions'))
            ->groupBy('page_url')
            ->orderBy('visits', 'desc')
            ->paginate(15);

        // Visitor analytics
        $visitors = ChatSession::whereHas('apiKey', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->select('user_id', 'user_name', 'user_email',
                     DB::raw('count(*) as total_sessions'),
                     DB::raw('sum(message_count) as total_messages'),
                     DB::raw('avg(TIMESTAMPDIFF(SECOND, created_at, ended_at)) as avg_duration'))
            ->groupBy('user_id', 'user_name', 'user_email')
            ->orderBy('total_sessions', 'desc')
            ->paginate(15);

        return view('admin.reports.analytics', compact('pages', 'visitors'));
    }


    public function chatLogsReport()
    {
        $user = auth()->user();
        
        $query = ChatLog::whereHas('session.apiKey', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Apply filters
        $type = request()->get('type');
        if ($type === 'user') {
            $query->where('sender', 'user');
        } elseif ($type === 'bot') {
            $query->where('sender', 'bot');
        }

        $dateRange = request()->get('date_range');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($dateRange === 'week') {
            $query->where('created_at', '>=', Carbon::now()->subWeek());
        } elseif ($dateRange === 'month') {
            $query->where('created_at', '>=', Carbon::now()->subMonth());
        }

        // Get chat logs with pagination
        $chatLogs = $query->with(['session', 'session.apiKey'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Prepare data for charts
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);
        $timelineLabels = [];
        $timelineData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateString = $currentDate->format('M d');
            $timelineLabels[] = $dateString;
            
            $count = $query->clone()
                ->whereDate('created_at', $currentDate)
                ->count();
                
            $timelineData[] = $count;
            $currentDate->addDay();
        }

        // Count message types
        $userLogsCount = $query->clone()->where('sender', 'user')->count();
        $botLogsCount = $query->clone()->where('sender', 'bot')->count();

        return view('admin.reports.chat_logs', compact(
            'chatLogs',
            'timelineLabels',
            'timelineData',
            'userLogsCount',
            'botLogsCount'
        ));
    }
}