# Fix Course Creation admin_id NULL Error

## Steps:
- [x] 1. Create this TODO.md
- [x] 2. Edit app/Http/Controllers/Admin/CourseController.php store() method to set admin_id = auth()->id()
- [x] 3. Clear Laravel caches
- [x] 4. Test course creation as admin and prof
- [x] 5. Verify database entry
- [x] 6. Complete task

**Fixed!** Course creation now works - admin_id properly set to current user for both admin and prof roles. No more SQL integrity violation.

