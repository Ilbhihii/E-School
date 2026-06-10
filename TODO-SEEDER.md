# Subject-Class Attachment Seeder Fix

## Plan Steps
1. ✅ [DONE] Create this TODO.md
2. ✅ [DONE] Edit `database/seeders/TestSeeder.php`: 
   - Remove deprecated `'class_id'` from Subject::factory()
   - Create 3 ClassRooms on same Level
   - Create Subject without class_id ('Français')
   - `$subject->classes()->attach([$classroom1->id, $classroom2->id, $classroom3->id]);`
3. [SKIP] Optionally fix `SubjectSeeder.php`
4. [SKIP] Add SubjectSeeder to DatabaseSeeder
5. ✅ [DONE] Test: `php artisan migrate:fresh --seed` (succeeded, DatabaseSeeder called TestSeeder)
6. ✅ [DONE] Verify: Subject 'Français' created with 3 classes attached via pivot (run tinker to confirm)
7. ✅ [DONE] Complete task
