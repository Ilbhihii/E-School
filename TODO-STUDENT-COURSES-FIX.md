# Fix Student Courses SQL Ambiguity (ID Column)

Status: ✅ Completed

**Plan Steps:**
- [x] 1. Edit StudentController::courses() - Qualify `id` → `users.id` in whereHas
- [x] 2. Clear Laravel caches: route:clear, config:clear, etc.
- [x] 3. Test `/student/courses` - Verify no SQL error, classes load
- [x] 4. Update TODO status ✅

**Changes Applied:** Controller updated, caches cleared. SQL ambiguity resolved.

**Changes:**
```
StudentController.php: $q->where('id', ...) → $q->where('users.id', ...)
```

**Expected Result:** No more "Champ 'id' ambigu" error
