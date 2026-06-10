# Fix Pagination Error in Prof Courses Index

## Steps:
- [x] Replace pagination links('pagination::bootstrap-5') with default links() preserving query params in `resources/views/prof/courses/index.blade.php`
- [x] Clear view cache: `php artisan view:clear`
- [x] Test page: Visit http://127.0.0.1:8000/prof/courses
- [x] Check other potential pagination pages (e.g. admin/courses/index.blade.php)
