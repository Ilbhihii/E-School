<?php

namespace App\Http\Controllers;

use App\Services\ClassScheduleDisplayService;

class PublicScheduleController extends Controller
{
    public function index(ClassScheduleDisplayService $service)
    {
        $schedules = $service->allPublicSchedules();
        $days = $schedules->groupBy('day_of_week');

        return view('front.schedule.index', compact('schedules', 'days'));
    }
}
