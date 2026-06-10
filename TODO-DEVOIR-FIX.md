# Fix Route [prof.devoir.index] Error

Status: Planning complete, implementing.

## Steps:
1. [ ] Create DevoirController.php - Basic CRUD for Assignment model (repurposed for prof homework assignments).
2. [ ] php artisan route:clear && php artisan serve (if not running).
3. [ ] Test navigation to /prof/devoir from /prof/absences.
4. [ ] Add migration for missing fields: class_room_id, due_date, description to assignments table.
5. [ ] Update Assignment model: add fillable, belongsTo ClassRoom, hasMany submissions if needed.
6. [ ] Refine views if errors.

Progress will be updated.
