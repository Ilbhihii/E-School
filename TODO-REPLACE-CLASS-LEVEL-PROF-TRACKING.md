# TODO: Replace class_id with level_id in Prof Courses

- [x] 1. Create migration to drop class_id and ensure level_id on courses table
- [x] 2. Update Course model (remove class_id from fillable, remove classRoom relation)
- [x] 3. Update ProfController (create, store, edit, update, courses methods)
- [x] 4. Update prof/courses/create.blade.php (replace class select with level select)
- [x] 5. Update prof/courses/edit.blade.php (replace class select with level select + fix corrupted tail)
- [x] 6. Update prof/courses/index.blade.php (replace classRoom display with level display)
- [x] 7. Update CourseFactory (replace class_id with level_id)
- [x] 8. Run php artisan migrate

All tasks completed successfully! Migration executed in 61.56ms.
