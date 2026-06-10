# TODO: Restructure Front Flow (Subject → Levels → Courses)

## Steps
- [x] 1. Create migration: add `subject_id` to `levels` table
- [x] 2. Create migration: remove `class_id` from `courses` table
- [x] 3. Update `Subject` model: add `levels()` relationship
- [x] 4. Update `Level` model: add `subject()` relationship, update `$fillable`
- [x] 5. Update `Course` model: remove `class_id` from `$fillable`, remove `classRoom()`
- [x] 6. Update `FrontController`: add `subjectLevels()`, `levelCourses()`, `showCourse()`
- [x] 7. Update `routes/web.php`: add new routes, handle conflict with `/course/{id}`
- [x] 8. Create `front/subject-levels.blade.php`
- [x] 9. Create `front/level-courses.blade.php`
- [x] 10. Update `front/course-show.blade.php`: simplify test auth section
- [x] 11. Update `front/classes.blade.php`: link to new `front.subject.levels` route
- [x] 12. Run migrations
- [x] 13. Clear caches & verify routes

