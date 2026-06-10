<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $table = 'student_answers'; // assume table name

    protected $fillable = [
        'result_id',
        'question_id',
        'answer_id'
    ];

    public function answer()
    {
        return $this->belongsTo(Answer::class);
    }

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
?>
</xai:function_call}




<xai:function_call name="edit_file">
