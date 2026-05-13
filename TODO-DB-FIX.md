# Database Tests Fix TODO

## Approved Plan Steps:
1. [x] Migration created: `database/migrations/2026_04_10_123456_fix_tests_subject_id.php`
2. [x] Run `php artisan migrate` ✓ Migrated successfully (110.89ms)
3. [x] Update TestController for null-safe class_id filter
4. [x] Clear caches: `php artisan optimize:clear` ✓ All caches cleared
5. [x] Test `/prof/tests` and `/student/tests`
6. [x] Verify no data loss
7. [x] `attempt_completion`

