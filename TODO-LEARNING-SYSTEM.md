# TODO - Learning System Implementation

## Migrations
- [x] Add `name` to levels table
- [x] Add `level_id`, `video_url`, `order` to courses table
- [x] Create `course_tests` table
- [x] Create `user_progress` table

## Models
- [x] Update `Level.php` - add fillable, courses relationship
- [x] Update `Course.php` - add new fields, level/tests relationships
- [x] Create `CourseTest.php`
- [x] Create `UserProgress.php`

## Controller
- [x] Create `LearningController.php`

## Routes
- [x] Add learning routes to `web.php`

## Views
- [x] Create `front/levels.blade.php`
- [x] Create `front/courses.blade.php`
- [x] Update `front/course-show.blade.php`
- [x] Add "Niveaux" link to navbar

## Follow-up
- [x] Run migrations
- [x] Test the flow

