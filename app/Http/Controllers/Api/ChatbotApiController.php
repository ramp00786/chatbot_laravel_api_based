<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\ChatbotQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\DB;

class ChatbotApiController extends Controller
{
    public function startSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $apiKey = ApiKey::where('key', $request->header('X-API-KEY'))
                  ->where('is_active', true)
                  ->firstOrFail();

        // Get location data
        $position = Location::get($request->ip());
        $location = $position ? ($position->cityName . ', ' . $position->countryName) : 'Unknown';

        // Detect device type
        $userAgent = $request->header('User-Agent');
        $deviceType = $this->detectDeviceType($userAgent);

        $session = ChatSession::create([
            'api_key_id' => $apiKey->id,
            'user_name' => $request->name,
            'user_email' => $request->email,
            'user_mobile' => $request->mobile,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device_type' => $deviceType,
            'location' => $location,
            'started_at' => now()
        ]);

        return response()->json([
            'session_id' => $session->id,
            'message' => 'Chat session started successfully'
        ]);
    }

    public function getQuestions(Request $request)
    {
        

        $apiKey = ApiKey::where('key', $request->header('X-API-KEY'))
                  ->where('is_active', true)
                  ->firstOrFail();

        $questions = ChatbotQuestion::where('user_id', $apiKey->user_id)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->with('children');
            }])
            ->get();

        return response()->json($questions);
    }

    public function saveMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:chat_sessions,id',
            'message' => 'required|string'            
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Verify session belongs to API key owner
        $apiKey = ApiKey::where('key', $request->header('X-API-KEY'))
                  ->where('is_active', true)
                  ->firstOrFail();

        $session = ChatSession::where('id', $request->session_id)
                   ->where('api_key_id', $apiKey->id)
                   ->firstOrFail();

        $chatMessage = ChatMessage::create([
            'chat_session_id' => $request->session_id,
            'is_from_user' => 1,
            'message' => $request->message
        ]);

        return response()->json([
            'message' => 'Chat message saved successfully',
            'data' => $chatMessage
        ]);
    }

    public function endSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|exists:chat_sessions,id',
            'api_key' => 'sometimes|required' // For beacon requests
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $apiKeyValue = $request->header('X-API-KEY') ?? $request->input('api_key');

        // Verify session belongs to API key owner
        $apiKey = ApiKey::where('key', $apiKeyValue)
              ->where('is_active', true)
              ->firstOrFail();

        $session = ChatSession::where('id', $request->session_id)
                   ->where('api_key_id', $apiKey->id)
                   ->firstOrFail();

        $session->update([
            'ended_at' => now()
        ]);

        return response()->json([
            'message' => 'Chat session ended successfully'
        ]);
    }

    private function detectDeviceType($userAgent)
    {
        if (stripos($userAgent, 'mobile') !== false) {
            return 'Mobile';
        } elseif (stripos($userAgent, 'tablet') !== false) {
            return 'Tablet';
        } elseif (stripos($userAgent, 'windows') !== false || stripos($userAgent, 'macintosh') !== false) {
            return 'Desktop';
        } else {
            return 'Other';
        }
    }

    public function logEvent(Request $request)
    {
        $request->validate([
            'session_id' => 'required',
            'sender' => 'required|in:bot,user',
            'message' => 'required|string',
            'type' => 'nullable|string',
            'parent_id' => 'nullable|integer'
        ]);

        DB::table('chat_logs')->insert([
            'session_id' => $request->session_id,
            'sender' => $request->sender,
            'message' => $request->message,
            'type' => $request->type,
            'parent_id' => $request->parent_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}