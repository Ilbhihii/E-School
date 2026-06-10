# TODO-FIX-CLASS-ID-COURSES-ERROR.md

## Status: [IN PROGRESS] ⏳

### 1. ✅ [x] Created this TODO

### 2. [ ] Run migration to add class_id column to courses table
```bash
php artisan migrate --path=database/migrations/2026_04_23_000000_ensure_class_id_on_courses.php
```

### 3. [ ] Fix Course.php model - add classRoom() relationship

### 4. [ ] Fix StudentController.php dashboard() method - replace class_id queries with relationships

### 5. [ ] Fix remaining StudentController methods (courses, assignments, etc.)

### 6. [ ] Clear Laravel caches
```bash
php artisan cache:clear &amp;&amp; php artisan route:clear &amp;&amp; php artisan view:clear
```

### 7. [ ] Test /student/dashboard ✅ COMPLETE

