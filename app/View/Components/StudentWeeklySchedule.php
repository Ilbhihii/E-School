<?php

namespace App\View\Components;

use App\Services\ClassScheduleDisplayService;
use Illuminate\View\Component;

class StudentWeeklySchedule extends Component
{
    public $limit;

    public function __construct($limit = 6)
    {
        $this->limit = max(1, (int) $limit);
    }

    public function render()
    {
        $occurrences = collect();

        if (auth()->check()) {
            $occurrences = app(ClassScheduleDisplayService::class)
                ->forStudent(auth()->user(), now(), 14, $this->limit);
        }

        return view('components.student-weekly-schedule', compact('occurrences'));
    }
}
