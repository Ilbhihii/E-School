# TODO: Replace class_id with level_id in Prof Courses

- [ ] 1. Create migration to drop class_id and ensure level_id on courses table
- [ ] 2. Update Course model (remove class_id from fillable, remove classRoom relation)
- [ ] 3. Update ProfController (create, store, edit, update, courses methods)
- [ ] 4. Update prof/courses/create.blade.php (replace class select with level select)
- [ ] 5. Update prof/courses/edit.blade.php (replace class select with level select + fix corrupted tail)
- [ ] 6. Update prof/courses/index.blade.php (replace classRoom display with level display)
- [ ] 7. Run php artisan migrate

