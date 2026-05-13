<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Result;
use App\Models\StudentAnswer;

class BackfillStudentAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-student-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill StudentAnswer records from existing Result.answers JSON';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        Result::chunk(100, function ($results) use (&$count) {
            foreach ($results as $result) {
                if ($result->answers) {
                    $decoded = json_decode($result->answers, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $questionId => $answerIds) {
                            if (is_array($answerIds)) {
                                foreach ($answerIds as $answerId) {
                                    StudentAnswer::updateOrCreate(
                                        [
                                            'result_id' => $result->id,
                                            'question_id' => (int) $questionId,
                                            'answer_id' => (int) $answerId,
                                        ]
                                    );
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        });

        $this->info("Backfill complete. Created/updated {$count} StudentAnswer records.");
    }
}
?>
