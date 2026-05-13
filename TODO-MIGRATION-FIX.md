# Migration Fix TODO

## Steps:
- [x] 1. Edit database/migrations/2026_03_03_110000_rename_class_room_id_in_subjects_table.php to fix up() method (make idempotent: check if class_room_id exists before updating/dropping).
    - [x] 2. Fixed add_user_id_to_courses_table (add hasColumn check for duplicate column).
- [x] 3. Verified all migrations complete successfully (migrate:fresh ran without errors, DB reset and fully migrated).
- [ ] 4. Verify with php artisan migrate:status (all Ran? Yes).
- [ ] 5. Optionally: php artisan db:seed.
- [ ] 6. Test app (register, login, etc.).

Step 1 completed: Migration file fixed. Running migrate:fresh to test and reset DB cleanly.

