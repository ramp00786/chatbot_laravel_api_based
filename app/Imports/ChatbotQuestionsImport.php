<?php

namespace App\Imports;

use App\Models\ChatbotQuestion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Auth;



class ChatbotQuestionsImport implements WithMultipleSheets
{
    protected $userId;
    
    public function __construct()
    {
        // $this->userId = Auth::id();
        $this->userId = 1;
    }
    
    public function sheets(): array
    {
        return [
            'a. After Class 10th' => new After10Import($this->userId),
            'b. After Class 12th' => new After12Import($this->userId),
            'After Polytechnic' => new AfterPolytechnicImport($this->userId),
            'After Graduation' => new AfterGraduationImport($this->userId),
        ];

      
    }
    
    public static function createWorksheetQuestions($userId)
    {
        $worksheetNames = [
            'a. After Class 10th',
            'b. After Class 12th',
            'After Polytechnic',
            'After Graduation'
        ];
        
        $existingQuestions = ChatbotQuestion::where('parent_id', 5)
            ->pluck('question')
            ->toArray();
            
        foreach ($worksheetNames as $name) {
            if (!in_array($name, $existingQuestions)) {
                ChatbotQuestion::create([
                    'user_id' => $userId,
                    'parent_id' => 5,
                    'question' => $name,
                    'answer' => null,
                    'answer_type' => null,
                    'answer_data' => null,
                    'is_final' => 0,
                    'enable_input' => 0,
                ]);
            }
        }
        
        // Remove any worksheet questions that no longer exist
        // ChatbotQuestion::where('parent_id', 5)
        //     ->whereNotIn('question', $worksheetNames)
        //     ->delete();
    }
}

class BaseCourseImport implements ToCollection
{
    protected $userId;
    protected $parentQuestion;
    
    public function __construct($userId, $parentQuestion)
    {
        $this->userId = $userId;
        $this->parentQuestion = $parentQuestion;
    }
    
    public function collection(Collection $rows)
    {
        // Get or create the worksheet parent question
        $worksheetQuestion = ChatbotQuestion::firstOrCreate(
            [
                'question' => $this->parentQuestion,
                'parent_id' => 5
            ],
            [
                'user_id' => $this->userId,
                'answer' => null,
                'answer_type' => null,
                'answer_data' => null,
                'is_final' => 0,
                'enable_input' => 0,
            ]
        );
        
        // Get or create the "Course" question under worksheet
        $courseQuestion = ChatbotQuestion::firstOrCreate(
            [
                'question' => 'Course',
                'parent_id' => $worksheetQuestion->id
            ],
            [
                'user_id' => $this->userId,
                'answer' => null,
                'answer_type' => null,
                'answer_data' => null,
                'is_final' => 0,
                'enable_input' => 0,
            ]
        );
        
        $currentCategory = null;
        $processedCourses = [];
        
        foreach ($rows as $row) {
            // Skip header row and empty rows
            if ($row[0] === 'Course' || empty($row[0])) {
                continue;
            }
            
            // Trim all values
            $row = $row->map(function ($item) {
                return is_string($item) ? trim($item) : $item;
            });
            
            // Check if this is a category row (empty duration)
            if (empty($row[1])) {
                $currentCategory = ChatbotQuestion::firstOrCreate(
                    [
                        'question' => $row[0],
                        'parent_id' => $courseQuestion->id
                    ],
                    [
                        'user_id' => $this->userId,
                        'answer' => null,
                        'answer_type' => null,
                        'answer_data' => null,
                        'is_final' => 0,
                        'enable_input' => 0,
                    ]
                );
                continue;
            }
            
            // Create course under current category or directly under Course if no category
            $parentId = $currentCategory ? $currentCategory->id : $courseQuestion->id;
            
            $course = ChatbotQuestion::firstOrCreate(
                [
                    'question' => $row[0],
                    'parent_id' => $parentId
                ],
                [
                    'user_id' => $this->userId,
                    'answer' => null,
                    'answer_type' => null,
                    'answer_data' => null,
                    'is_final' => 0,
                    'enable_input' => 0,
                ]
            );
            
            // Track processed courses to avoid duplicate details
            if (!in_array($course->id, $processedCourses)) {
                // Add course details if they exist
                if (!empty($row[1])) {
                    $this->createCourseDetail($course->id, 'Duration', 'simple', $row[1]);
                }
                
                if (!empty($row[2])) {
                    $this->createCourseDetail($course->id, 'Fees With Caution Money', 'simple', $row[2]);
                }
                
                if (!empty($row[3])) {
                    $this->createCourseDetail($course->id, 'Link', 'rich_text', '<a href="'.$row[3].'" target="_blank">'.$row[3].'</a>');
                }
                
                if (!empty($row[4])) {
                    $this->createCourseDetail($course->id, 'Eligibility Criterion', 'rich_text', '<p>'.$row[4].'</p>');
                }
                
                $processedCourses[] = $course->id;
            }
        }
    }
    
    protected function createCourseDetail($parentId, $question, $answerType, $answer)
    {
        ChatbotQuestion::updateOrCreate(
            [
                'question' => $question,
                'parent_id' => $parentId
            ],
            [
                'user_id' => $this->userId,
                'answer' => $answer,
                'answer_type' => $answerType,
                'answer_data' => null,
                'is_final' => 1,
                'enable_input' => 0,
            ]
        );
    }
}

class After10Import extends BaseCourseImport
{
    public function __construct($userId)
    {
        parent::__construct($userId, 'a. After Class 10th');
    }
}

class After12Import extends BaseCourseImport
{
    public function __construct($userId)
    {
        parent::__construct($userId, 'b. After Class 12th');
    }
}

class AfterPolytechnicImport extends BaseCourseImport
{
    public function __construct($userId)
    {
        parent::__construct($userId, 'After Polytechnic');
    }
}

class AfterGraduationImport extends BaseCourseImport
{
    public function __construct($userId)
    {
        parent::__construct($userId, 'After Graduation');
    }
}