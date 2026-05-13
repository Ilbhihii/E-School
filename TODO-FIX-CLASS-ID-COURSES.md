# Fix class_id on courses table - TODO

## Plan
- [x] Create idempotent migration to add `class_id` back to `courses` table
- [x] Update `app/Models/Course.php` (`$fillable` + `classRoom()` relationship)
- [x] Run `php artisan migrate`
- [x] Clear caches
- [x] Test the failing URL `/matieres/1/classes/2/courses`

**Status: Complete**

