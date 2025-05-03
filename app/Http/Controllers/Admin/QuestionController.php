<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = ChatbotQuestion::with('parent')
            ->withCount('children')
            ->latest()
            ->get();

        return view('admin.questions.index', compact('questions'));
    }

    public function children(ChatbotQuestion $question)
    {
        $children = $question->children()->latest()->get();
        return response()->json($children);
    }

    public function create()
    {
        // $parents = ChatbotQuestion::where('user_id', auth()->id())
        //     ->where('is_final', false)
        //     ->get();

        $questions = ChatbotQuestion::with('parent')
        ->orderBy('parent_id')
        ->orderBy('question')
        ->get();

        return view('admin.questions.create', [
            'questions' => $questions,
            'hasQuestions' => $questions->isNotEmpty(), // New flag
            'selectedParent' => request('parent_id') // Add this line
        ]);
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'question' => 'required',
    //         'parent_id' => 'nullable|exists:chatbot_questions,id',
    //         'answer' => 'required_if:is_final,true',
    //         'is_final' => 'boolean',
    //         'enable_input' => 'boolean'
    //     ]);

    //     ChatbotQuestion::create([
    //         'user_id' => auth()->id(),
    //         'parent_id' => $request->parent_id,
    //         'question' => $request->question,
    //         'answer' => $request->answer,
    //         'is_final' => $request->is_final ?? false,
    //         'enable_input' => $request->enable_input ?? false
    //     ]);

    //     return redirect()->route('admin.questions.index')
    //         ->with('success', 'Question added successfully');
    // }


    // In the store method:
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'question' => 'required',
    //         'parent_id' => 'nullable|exists:chatbot_questions,id',
    //         'answer' => 'required_if:is_final,true',
    //         'is_final' => 'boolean',
    //         'enable_input' => 'boolean',
    //         'answer_type' => 'nullable|in:text,rich_text,file,youtube',
        
    //         'answer_file' => 'nullable|required_if:answer_type,file|file|mimes:jpg,png,gif,pdf,doc,docx,ppt,pptx,mp4,mov',
    //         'answer_youtube' => 'nullable|required_if:answer_type,youtube|url',
    //     ]);

    //     $answer = '';
    //     $answerData = [];

    //     switch($request->answer_type) {
    //         case 'text':
    //             $answer = $request->answer;
    //             break;
    //         case 'rich_text':
    //             $answer = $request->answer_rich_text;
    //             $answerData['type'] = 'rich_text';
    //             break;
    //         case 'file':
    //             if ($request->hasFile('answer_file')) {
    //                 $path = $request->file('answer_file')->store('question_files', 'public');
    //                 $answer = $path;
    //                 $answerData = [
    //                     'type' => 'file',
    //                     'original_name' => $request->file('answer_file')->getClientOriginalName(),
    //                     'mime_type' => $request->file('answer_file')->getClientMimeType()
    //                 ];
    //             }
    //             break;
    //         case 'youtube':
    //             $url = $request->answer_youtube;
    //             $videoId = $this->extractYouTubeId($url);
    //             if ($videoId) {
    //                 $answer = $videoId;
    //                 $answerData = [
    //                     'type' => 'youtube',
    //                     'url' => $url
    //                 ];
    //             }
    //             break;
    //     }

    //     ChatbotQuestion::create([
    //         'user_id' => auth()->id(),
    //         'parent_id' => $request->parent_id,
    //         'question' => $request->question,
    //         'answer' => $request->answer_type,
    //         'answer_data' => json_encode($answerData),
    //         'is_final' => $request->is_final ?? false,
    //         'enable_input' => $request->enable_input ?? false
    //     ]);

    //     return redirect()->route('admin.questions.index')
    //         ->with('success', 'Question added successfully');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'parent_id' => 'nullable|exists:chatbot_questions,id',
            'answer' => 'required_if:is_final,true',
            'is_final' => 'boolean',
            'enable_input' => 'boolean',
            'answer_type' => 'nullable|in:text,rich_text,file,youtube',
        
            'answer_file' => 'nullable|required_if:answer_type,file|file|mimes:jpg,png,gif,pdf,doc,docx,ppt,pptx,mp4,mov',
            'answer_youtube' => 'nullable|required_if:answer_type,youtube|url',
        ]);

        $answer = '';
        $answerData = [];
        

        switch($request->answer_type) {
            case 'text':
                $answer = $request->answer;
                $answerData = ['type' => 'text'];
                break;
                
            case 'rich_text':
                $answer = $request->answer_rich_text;
                $answerData = ['type' => 'rich_text'];
                break;
                
            case 'file':
                if ($request->hasFile('answer_file')) {
                    $path = $request->file('answer_file')->store('question_files', 'public');
                    $answer = $path;
                    $answerData = [
                        'type' => 'file',
                        'original_name' => $request->file('answer_file')->getClientOriginalName(),
                        'mime_type' => $request->file('answer_file')->getClientMimeType(),
                        'file_path' => $path // Explicitly storing file path
                    ];
                }
                
                break;
                
            case 'youtube':
                $url = $request->answer_youtube;
                $videoId = $this->extractYouTubeId($url);
                if ($videoId) {
                    $answer = $videoId;
                    $answerData = [
                        'type' => 'youtube',
                        'url' => $url,
                        'embed_url' => 'https://www.youtube.com/embed/'.$videoId
                    ];
                }
                break;
        }

        

        ChatbotQuestion::create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'question' => $request->question,
            'answer' => $answer, // Store the actual answer content, not the type
            'answer_type' => $request->answer_type, // Store the type separately
            'answer_data' => json_encode($answerData),
            'is_final' => $request->is_final ?? false,
            'enable_input' => $request->enable_input ?? false
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question added successfully');
    }

    private function extractYouTubeId($url)
    {
        preg_match('%(?:youtube(?:nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        return $match[1] ?? null;
    }

    

    public function edit($id)
    {
        $question = ChatbotQuestion::where('user_id', auth()->id())
            ->findOrFail($id);

        $questions = ChatbotQuestion::with('parent')
        ->orderBy('parent_id')
        ->orderBy('question')
        ->get();

        $parents = ChatbotQuestion::where('user_id', auth()->id())
            ->where('is_final', false)
            ->where('id', '!=', $id)
            ->get();

        return view('admin.questions.edit', compact('question', 'parents', 'questions'));
    }

    // public function update(Request $request, $id)
    // {
    //     $question = ChatbotQuestion::where('user_id', auth()->id())
    //         ->findOrFail($id);

    //     $request->validate([
    //         'question' => 'required',
    //         'parent_id' => 'nullable|exists:chatbot_questions,id',
    //         'answer' => 'required_if:is_final,true',
    //         'is_final' => 'boolean',
    //         'enable_input' => 'boolean'
    //     ]);

    //     $question->update([
    //         'parent_id' => $request->parent_id,
    //         'question' => $request->question,
    //         'answer' => $request->answer,
    //         'is_final' => $request->is_final ?? false,
    //         'enable_input' => $request->enable_input ?? false
    //     ]);

    //     return redirect()->route('admin.questions.index')
    //         ->with('success', 'Question updated successfully');
    // }

    public function update(Request $request, $id)
    {
        $question = ChatbotQuestion::where('user_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'question' => 'required',
            'parent_id' => 'nullable|exists:chatbot_questions,id',
            'answer' => 'required_if:is_final,true',
            'is_final' => 'boolean',
            'enable_input' => 'boolean',
            'answer_type' => 'nullable|in:text,rich_text,file,youtube',

            'answer_file' => 'nullable|required_if:answer_type,file|file|mimes:jpg,png,gif,pdf,doc,docx,ppt,pptx,mp4,mov',
            'answer_youtube' => 'nullable|required_if:answer_type,youtube|url',
        ]);

        $answer = $question->answer; // default to existing answer
        $answerData = json_decode($question->answer_data, true) ?? [];

        switch($request->answer_type) {
            case 'text':
                $answer = $request->answer;
                $answerData = ['type' => 'text'];
                break;

            case 'rich_text':
                $answer = $request->answer_rich_text;
                $answerData = ['type' => 'rich_text'];
                break;

            case 'file':
                if ($request->hasFile('answer_file')) {
                    $path = $request->file('answer_file')->store('question_files', 'public');
                    $answer = $path;
                    $answerData = [
                        'type' => 'file',
                        'original_name' => $request->file('answer_file')->getClientOriginalName(),
                        'mime_type' => $request->file('answer_file')->getClientMimeType(),
                        'file_path' => $path,
                    ];
                }
                break;

            case 'youtube':
                $url = $request->answer_youtube;
                $videoId = $this->extractYouTubeId($url);
                if ($videoId) {
                    $answer = $videoId;
                    $answerData = [
                        'type' => 'youtube',
                        'url' => $url,
                        'embed_url' => 'https://www.youtube.com/embed/'.$videoId,
                    ];
                }
                break;
        }

        $question->update([
            'parent_id' => $request->parent_id,
            'question' => $request->question,
            'answer' => $answer,
            'answer_type' => $request->answer_type,
            'answer_data' => json_encode($answerData),
            'is_final' => $request->is_final ?? false,
            'enable_input' => $request->enable_input ?? false
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully');
    }


    public function destroy($id)
    {
        $question = ChatbotQuestion::where('user_id', auth()->id())
            ->findOrFail($id);
        
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully');
    }
}