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

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
            'parent_id' => 'nullable|exists:chatbot_questions,id',
            'answer' => 'required_if:is_final,true',
            'is_final' => 'boolean',
            'enable_input' => 'boolean'
        ]);

        ChatbotQuestion::create([
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'question' => $request->question,
            'answer' => $request->answer,
            'is_final' => $request->is_final ?? false,
            'enable_input' => $request->enable_input ?? false
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question added successfully');
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

    public function update(Request $request, $id)
    {
        $question = ChatbotQuestion::where('user_id', auth()->id())
            ->findOrFail($id);

        $request->validate([
            'question' => 'required',
            'parent_id' => 'nullable|exists:chatbot_questions,id',
            'answer' => 'required_if:is_final,true',
            'is_final' => 'boolean',
            'enable_input' => 'boolean'
        ]);

        $question->update([
            'parent_id' => $request->parent_id,
            'question' => $request->question,
            'answer' => $request->answer,
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