<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\UploadQuestionCSVController;
use App\Http\Controllers\EndChatSessionController;
use App\Http\Controllers\Admin\ChatbotQuestionImportController;
use App\Models\ChatbotQuestion;
use App\Http\Controllers\FileController;



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'chatbot'])->name('chatbot');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'redirectToDashboard'])->name('chatbot');



// Admin Routes (protected by auth middleware)
// Route::middleware('auth')->group(function() {
//     Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    
    
//     // API Key Management
//     Route::get('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('admin.api-keys');
//     Route::post('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'store']);
//     Route::delete('/api-keys/{id}', [App\Http\Controllers\Admin\ApiKeyController::class, 'destroy']);
    
//     // Question Management
//     Route::get('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.questions.index');
//     Route::get('/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create']);
//     Route::post('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store']);
//     Route::get('/questions/{id}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit']);
//     Route::put('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'update']);
//     Route::delete('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy']);
    
//     // CSV Import
//     Route::get('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'importForm']);
//     Route::post('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'import']);
    
//     // Statistics
//     Route::get('/statistics', [App\Http\Controllers\Admin\StatisticController::class, 'import']);
// });

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/{session_id}', [App\Http\Controllers\Admin\DashboardController::class, 'showSessionHistory'])->name('admin.dashboard.session_id');


    // API Key Management
    Route::get('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('admin.api-keys');
    Route::post('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->name('admin.api-keys.store');
    Route::delete('/api-keys/{id}', [App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->name('admin.api-keys.destroy');

    // Question Management
    Route::get('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.questions.index');
    Route::get('/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('admin.questions.create');
    Route::post('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('admin.questions.store');
    Route::get('/questions/{id}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::put('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('admin.questions.destroy');

    // CSV Import
    Route::get('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'importForm'])->name('admin.questions.import-form');
    Route::post('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('admin.questions.import');

    // Statistics
    // Route::get('/statistics', [App\Http\Controllers\Admin\StatisticController::class, 'import'])->name('admin.statistics.import');

    Route::get('/end-inactive-sessions', [EndChatSessionController::class, 'index'])->name('admin.end-inactive-sessions');
    Route::get('/stream-end-inactive-sessions', [EndChatSessionController::class, 'endInactiveSessionsSSE']);


    Route::get('/get-file-type/{id}', [App\Http\Controllers\FileTypeController::class, 'getFileType']);


    Route::get('/get-answer-data/{id}', function($id) {
        $question = ChatbotQuestion::findOrFail($id);
        
        return response()->json([
            'answer_data' => $question->answer_data,
            'answer_content' => $question->answer,
        ]);
    });


    Route::get('/file/view/{filename}', [FileController::class, 'view'])->name('file.view');
    Route::get('/file/download/{filename}', [FileController::class, 'download'])->name('file.download');

    // Route::get('/file/view/{filename}', function($filename){
    //     return "asdf".$filename;
    // });

    Route::get('/csv', [UploadQuestionCSVController::class, 'index']);
    Route::get('/csv-reserval', [UploadQuestionCSVController::class, 'reverseInsertions']);

});


// Admin Dashboard
Route::prefix('admin')->middleware('auth')->group(function() {
    Route::get('questions/{question}/children', [App\Http\Controllers\Admin\QuestionController::class, 'children'])
        ->name('admin.questions.children');
    Route::resource('questions', App\Http\Controllers\Admin\QuestionController::class);
});


// routes/web.php (add these to your admin routes)
Route::prefix('admin')->group(function() {
    // Reports
    Route::get('/reports/sessions', [ReportsController::class, 'sessionsReport'])->name('admin.reports.sessions');
    Route::get('/reports/sessions/active', [ReportsController::class, 'sessionsReport'])->name('admin.reports.sessions.active');
    Route::get('/reports/messages', [ReportsController::class, 'messagesReport'])->name('admin.reports.messages');
    Route::get('/reports/locations', [ReportsController::class, 'locationsReport'])->name('admin.reports.locations');
    Route::get('/reports/devices', [ReportsController::class, 'devicesReport'])->name('admin.reports.devices');
    Route::get('/reports/analytics', [ReportsController::class, 'analyticsReport'])->name('admin.reports.analytics');

    Route::get('/reports/chat-logs', [ReportsController::class, 'chatLogsReport'])->name('admin.reports.chat_logs');
    Route::get('/chat/history/{session}', [ChatHistoryController::class, 'show'])
        ->name('admin.chat.history.show');
    
    // Chart data endpoints
    Route::get('/dashboard/chart-data/{chart}/{range}', [DashboardController::class, 'chartData']);
    Route::get('/dashboard/engagement-data/{range}', [DashboardController::class, 'engagementData']);
    Route::get('/dashboard/update-time-range/{days}', [DashboardController::class, 'updateTimeRange']);

    

    
});


Route::middleware(['auth', 'admin.email'])->group(function () {
    // Chatbot question import routes
    Route::get('/chatbot/import', [ChatbotQuestionImportController::class, 'showImportForm'])->name('chatbot.import.form');
    Route::post('/chatbot/import', [ChatbotQuestionImportController::class, 'import'])->name('chatbot.import');
});




