# Schedule PRO Features Implementation
Status: In Progress

## Steps:
- [x] 1. Create TODO-SCHEDULE-PRO.md
- [x] 2. Add class filter to Prof\\ScheduleController::data() with Request $request, where class_id filter, load class->name for title
- [x] 3. Fix controller queries to use 'prof_id' instead of 'teacher_id'
- [x] 4. Update prof/schedule.blade.php: Replace eventDidMount if-chain with colors object (add Français)
- [x] 5. Add class filter dropdown UI above calendar, fetch prof's classes via new route/API, reload calendar on change with class_id param
- [x] 6. Test functionality
- [x] 7. Update main TODO.md, attempt_completion

**COMPLETED**

## Notes:
Approved plan. Prof's classes from where user class_id where role='prof' or pivot.

