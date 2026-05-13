# TODO: Fix MethodNotAllowedHttpException for admin/users/{id} update

## Steps:
- [x] 1. Add `edit` method to app/Http/Controllers/Admin/UserController.php
- [x] 2. Add GET route `/admin/users/{user}/edit` to routes/web.php
- [x] 3. Create `resources/views/admin/users/edit.blade.php`
- [x] 4. Update inline form in `resources/views/admin/users/without-class.blade.php` with JS confirmation
- [ ] 5. Add edit links in `resources/views/admin/users/index.blade.php`
- [ ] 6. Run `php artisan route:clear && php artisan config:clear`
- [ ] 7. Test: Visit /admin/users/without-class, assign class to user 3, verify no error

Current: Starting step 1.
