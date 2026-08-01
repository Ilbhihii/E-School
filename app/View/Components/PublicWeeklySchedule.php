<?php

namespace App\View\Components;

use App\Services\ClassScheduleDisplayService;
use Illuminate\View\Component;

class PublicWeeklySchedule extends Component
{
    public $limit;

    public function __construct($limit = 6)
    {
        $this->limit = max(1, (int) $limit);
    }

    public function render()
    {
        $schedules = app(ClassScheduleDisplayService::class)
            ->allPublicSchedules()
            ->take($this->limit)
            ->values();

        return view('components.public-weekly-schedule', compact('schedules'));
    }
}
