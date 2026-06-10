<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject_id',
        'duration',
        'create_by',
        'is_ai_generated'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->count();
    }

    public function getAverageScoreAttribute()
    {
        $avg = $this->results()->avg('score');
        return $avg ? round(($avg / $this->questions_count) * 20, 2) : null;
    }
}
?>

