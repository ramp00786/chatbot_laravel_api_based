<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatbotQuestion;
use Illuminate\Support\Facades\File;

class UploadQuestionCSVController extends Controller
{
    private $logFile = 'inserted_ids.json';

    private function insertQA($parentId, $question, $answer, $type = 'simple', &$log)
    {
        $parent = new ChatbotQuestion();
        $parent->user_id = 1;
        $parent->parent_id = $parentId;
        $parent->question = $question;
        $parent->answer_type = $type;
        $parent->answer = $answer;
        $parent->save();
        $log[] = $parent->id;
    }

    public function index()
    {
        echo "<pre>";
        $filename = "12th.csv";

        if (!file_exists($filename) || !is_readable($filename)) {
            exit("CSV file not found or not readable.");
        }

        $header = null;
        $insertedIds = [];

        if (($handle = fopen($filename, "r")) !== false) {
            while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    if (count($row) < 5) {
                        echo "Skipping row due to insufficient columns.\n";
                        continue;
                    }

                    $qinfo = ChatbotQuestion::where('question', $row[0])->first();

                    if ($qinfo) {
                        $parentId = $qinfo->id;

                        $linkUrl = htmlspecialchars($row[3], ENT_QUOTES, 'UTF-8');
                        $eligibilityText = htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8');

                        $this->insertQA($parentId, 'Duration', $row[1], 'simple', $insertedIds);
                        $this->insertQA($parentId, 'Fees With Caution Money', $row[2], 'simple', $insertedIds);
                        $this->insertQA($parentId, 'Link', '<p><a target="_blank" href="' . $linkUrl . '">' . $linkUrl . '</a></p>', 'rich_text', $insertedIds);
                        $this->insertQA($parentId, 'Eligibility Criterion', '<p>' . $eligibilityText . '</p>', 'rich_text', $insertedIds);
                    }
                }
            }
            fclose($handle);
        }

        // Store inserted IDs for potential rollback
        File::put(storage_path($this->logFile), json_encode($insertedIds, JSON_PRETTY_PRINT));

        echo "Inserted IDs stored in '{$this->logFile}'\n";
        echo "</pre>";
    }

    public function reverseInsertions()
    {
        echo "<pre>";

        $path = storage_path($this->logFile);

        if (!file_exists($path)) {
            exit("No rollback log file found.");
        }

        $ids = json_decode(File::get($path), true);

        if (!is_array($ids) || empty($ids)) {
            exit("No IDs to delete.");
        }

        // Reverse order to delete children before parents
        rsort($ids);

        foreach ($ids as $id) {
            $question = ChatbotQuestion::find($id);
            if ($question) {
                $question->delete();
                echo "Deleted ID: $id\n";
            }
        }

        // Optionally delete the log file after rollback
        File::delete($path);
        echo "Rollback complete. Log file deleted.\n";
        echo "</pre>";
    }
}
