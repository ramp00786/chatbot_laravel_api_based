<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = auth()->user()->apiKeys()->latest()->get();
        return view('admin.api-keys', compact('keys'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        do {
            $key = Str::random(64);
        } while (ApiKey::where('key', $key)->exists());

        $apiKey = auth()->user()->apiKeys()->create([
            'user_id' => auth()->id(), // Add this line
            'key' => $key,
            'name' => $request->name,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.api-keys')
            ->with('success', 'API key generated successfully. Key: ' . $key)
            ->with('new_key', $key); // Optional: if you want to highlight the new key
    }

    public function destroy($id)
    {
        $key = auth()->user()->apiKeys()->findOrFail($id);
        $key->delete();

        return redirect()->route('admin.api-keys')
            ->with('success', 'API Key revoked successfully');
    }
}