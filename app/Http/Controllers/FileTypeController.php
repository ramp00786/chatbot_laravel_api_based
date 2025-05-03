<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatbotQuestion; // replace with your actual model

class FileTypeController extends Controller
{
    public function getFileType($id)
    {
        $record = ChatbotQuestion::find($id); // Replace 'YourModel' with your model name

        if (!$record || empty($record->answer_data)) {
            return response()->json(['error' => 'File not found.'], 404);
        }

        $filePath = $record->answer;

        $fileType = $this->detectFileType($filePath);

        return response()->json([
            'file_path' => $filePath,
            'file_type' => $fileType
        ]);
    }

    private function detectFileType($filePath)
    {
        
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        $pdfExtensions   = ['pdf'];
        $docExtensions   = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $pdfExtensions)) {
            return 'pdf';
        } elseif (in_array($extension, $docExtensions)) {
            return 'doc';
        } else {
            return 'other';
        }
    }
}
