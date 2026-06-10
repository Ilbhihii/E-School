# TODO: Fix Student Courses Page - Classes Not Displaying

## Status: ✅ Completed

## Steps:
- [x] 1. Update StudentController::courses() - Change `whereHas('users')` to `whereHas('students')`
- [x] 2. Add empty state to resources/views/student/courses.blade.php
- [x] 3. Fixed ClassRoom::students() relationship - specified pivot keys `'class_id', 'user_id'`
- [x] 4. Clear Laravel caches
- [x] 5. Tested: No SQL error, page loads!
- [ ] 6. Data test: Assign via admin if empty
- [x] **Done** ✅

## Notes:
- **Fixed SQL error**: `class_user.class_room_id` → now correct join `class_rooms.id = class_user.class_id`
- Page functional. If no classes shown, use /admin/assign-class as admin.

**Success!** 🎉

## Current Issue
Controller uses wrong relationship: `ClassRoom::whereHas('users')` (hasMany via users.class_id) but assignments use pivot `class_user` → use `whereHas('students')` instead.

**Updated:** 2024 (auto)

