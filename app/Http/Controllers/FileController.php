<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function view($filename)
    {
        $path = "question_files/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $mimeType = Storage::disk('public')->mimeType($path);
        $fileContent = Storage::disk('public')->get($path);

        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }

    public function download($filename)
    {
        $path = "question_files/{$filename}";

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }
}
