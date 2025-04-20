<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-KEY');
       
        if (!$apiKey || !ApiKey::where('key', $apiKey)->where('is_active', true)->exists()) {
            return response()->json(['error' => 'Invalid or missing API key'], 401);
        }

        return $next($request);
    }
}