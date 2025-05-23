<?php

namespace App\Http\Controllers;

use App\Models\ChatSession;
use App\Models\ChatLog;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EndChatSessionController extends Controller
{

    public function index(){
        return view('admin.end_chat_session');
    }


    // public function endInactiveSessionsSSE(): StreamedResponse
    // {
    //     return response()->stream(function () {
    //         $this->line("🔍 Checking for inactive sessions (no activity for 5 minutes)...");

    //         $inactiveThreshold = now()->subMinutes(0.1);
    //         $activeSessions = ChatSession::whereNull('ended_at')->get();
    //         $this->line("Found {$activeSessions->count()} active sessions to check");
    //         $this->line("⏳ Processing sessions...");
    //         $this->line("");

    //         $endedCount = 0;

    //         foreach ($activeSessions as $session) {
    //             $lastActivity = ChatLog::where('session_id', $session->id)
    //                 ->latest('created_at')
    //                 ->value('created_at');

    //             if (!$lastActivity) {
    //                 $this->line("🛑 Session #{$session->id} - No activity found (new session)");
    //                 continue;
    //             }

    //             if ($lastActivity->lt($inactiveThreshold)) {
    //                 $session->update(['ended_at' => now()]);
    //                 $endedCount++;
    //                 $this->line("✅ Ended session #{$session->id} - Last activity was " .
    //                     $lastActivity->diffForHumans() . " ({$lastActivity->format('Y-m-d H:i:s')})");
    //             } else {
    //                 $this->line("✔️ Keeping session #{$session->id} - Active " .
    //                     $lastActivity->diffForHumans());
    //             }

    //             ob_flush();
    //             flush();
    //             usleep(300000); // 300ms delay for smooth output
    //         }

    //         $this->line("");
    //         $this->line("=================================");
    //         $this->line("🎉 Completed! Results:");
    //         $this->line("Total sessions ended: {$endedCount}");
    //         $this->line("Active sessions remaining: " . ($activeSessions->count() - $endedCount));
    //         $this->line("=================================");
    //     }, 200, [
    //         'Content-Type' => 'text/event-stream',
    //         'Cache-Control' => 'no-cache',
    //         'X-Accel-Buffering' => 'no', // for nginx
    //     ]);
    // }


    public function endInactiveSessionsSSE(): StreamedResponse
    {
        return response()->stream(function () {
            $this->line("🔍 Checking for inactive sessions (no activity for 5 minutes)...");

            $inactiveThreshold = now()->subMinutes(0.1);
            $activeSessions = ChatSession::whereNull('ended_at')->get();
            $this->line("Found {$activeSessions->count()} active sessions to check");
            $this->line("⏳ Processing sessions...");
            $this->line("");

            $endedCount = 0;

            foreach ($activeSessions as $session) {
                $lastActivity = ChatLog::where('session_id', $session->id)
                    ->latest('created_at')
                    ->value('created_at');

                // If no activity found at all, end the session
                if (!$lastActivity) {
                    $session->update(['ended_at' => now()]);
                    $endedCount++;
                    $this->line("🛑 Session #{$session->id} - No activity found (new session) - Ended");
                    continue;
                }

                // End session if either:
                // 1. Last activity is older than threshold, OR
                // 2. Session was created before threshold and has no recent activity
                if ($lastActivity->lt($inactiveThreshold) || 
                    ($session->created_at->lt($inactiveThreshold) && !$this->hasRecentActivity($session->id, $inactiveThreshold))) {
                    $session->update(['ended_at' => now()]);
                    $endedCount++;
                    $this->line("✅ Ended session #{$session->id} - Last activity was " .
                        $lastActivity->diffForHumans() . " ({$lastActivity->format('Y-m-d H:i:s')})");
                } else {
                    $this->line("✔️ Keeping session #{$session->id} - Active " .
                        $lastActivity->diffForHumans());
                }

                ob_flush();
                flush();
                usleep(300000); // 300ms delay for smooth output
            }

            $this->line("");
            $this->line("=================================");
            $this->line("🎉 Completed! Results:");
            $this->line("Total sessions ended: {$endedCount}");
            $this->line("Active sessions remaining: " . ($activeSessions->count() - $endedCount));
            $this->line("=================================");
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function hasRecentActivity($sessionId, Carbon $threshold): bool
    {
        return ChatLog::where('session_id', $sessionId)
            ->where('created_at', '>=', $threshold)
            ->exists();
    }

    private function line(string $message): void
    {
        echo "data: {$message}\n\n";
    }
}
