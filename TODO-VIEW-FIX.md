# Fix front/classes.blade.php href error

- [x] Step 1: Edit resources/views/front/classes.blade.php - replace $subject->classRoom->id with $subject->id in route()
- [x] Step 2: php artisan view:clear && php artisan config:clear
- [x] Step 3: Test http://127.0.0.1:8000/classes
- [x] Complete
