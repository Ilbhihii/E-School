<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Subject;

class AppServiceProvider extends ServiceProvider
{
    

    public function boot()
    {
        view()->composer('layouts.front', function ($view) {
            $religieux = \App\Models\Subject::where('type', 'religieux')->get();
            $scolaire = \App\Models\Subject::where('type', 'scolaire')->get();
            
            $subjectsGrouped = [
                'Matières Religieuses' => [
                    'subjects' => $religieux,
                    'color' => 'primary'
                ],
                'Matières Scolaires' => [
                    'subjects' => $scolaire,
                    'color' => 'success'
                ]
            ];
            
            $view->with('subjectsGrouped', $subjectsGrouped);
        });
    }
}
