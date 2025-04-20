<?php
// app/Models/ChatbotQuestion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatbotQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'question',
        'answer',
        'is_final',
        'enable_input'
    ];

    // Your existing relationships...
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    

    // app/Models/ChatbotQuestion.php
    public function isDescendantOf(ChatbotQuestion $parent): bool
    {
        $current = $this->parent;
        
        while ($current) {
            if ($current->id === $parent->id) {
                return true;
            }
            $current = $current->parent;
        }
        
        return false;
    }
}