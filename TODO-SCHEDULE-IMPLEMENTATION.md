# Schedule Fixes Implementation Plan

## Steps to Complete:

- [x] **Step 1:** Update `app/Models/Schedule.php`
  - Add `'date' => 'date'` to `$casts`
  - Replace `teacher_id` → `prof_id` in `$fillable`
  - Update `teacher(): BelongsTo` to use `'prof_id'`
  - Remove redundant `$dates = []`

  
- [x] **Step 2:** Fix formatting in `resources/views/admin/schedule/index.blade.php`
  - Replace verbose `{{ ($s->start_time instanceof ... }}` with `{{ $s->start_time?->format('H:i') }} → {{ $s->end_time?->format('H:i') }}`
  - Verify `$s->date?->format('d/m/Y')`
  - Add day logic: `{{ $s->date?->locale('fr')->translatedFormat('l') }}`


- [x] **Step 3:** Simplify `app/Http/Controllers/Prof/ScheduleController.php` data()
  - Change `Carbon::parse($item->start_time)->toIso8601String()` → `$item->start_time->toISOString()`
  - Same for end_time

- [x] **Step 4:** Update controllers for prof_id
  - In AdminScheduleController store(): map `'prof_id' => $request->teacher_id`
  - In ProfScheduleController store(): `'prof_id' => auth()->id()`
  - ProfScheduleController data(): where('prof_id'...)


- [x] **Step 5:** Clear caches
  - Run `php artisan route:clear; php artisan config:clear; php artisan view:clear`

- [x] **Step 6:** Test complete
  - Model now casts date/start_time/end_time properly for formatting
  - Admin blade simplified with French day names and clean time display
  - Controllers updated for prof_id, simplified ISO strings for FullCalendar
  - Caches cleared

All schedule date/time formatting fixes implemented per task requirements.


**Current Progress:** Starting implementation...

