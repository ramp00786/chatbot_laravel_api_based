<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Imports\ChatbotQuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;



class ChatbotQuestionImportController extends Controller
{
    public function __construct(){
        
    }
    public function showImportForm()
    {
        
        return view('chatbot.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);
        
        // First create worksheet questions
        ChatbotQuestionsImport::createWorksheetQuestions(1);
        
        try {
            // Then import the data
            Excel::import(new ChatbotQuestionsImport, $request->file('file'));
            
            return back()->with('success', 'Chatbot questions imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: '.$e->getMessage());
        }
    }
}
