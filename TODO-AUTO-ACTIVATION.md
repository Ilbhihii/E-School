# TODO: Auto-Redirect on Student Account Activation ✅

## Steps:

### ✅ 1. Add check-active route to routes/web.php
- Inside Route::middleware(['auth','role:student']) → prefix('student') group, before the inner middleware group.
- `Route::get('/check-active', [StudentController::class, 'checkActive'])->name('check.active');`

### ✅ 2. Add checkActive method to app/Http/Controllers/Student/StudentController.php
```
public function checkActive()
{
    return response()->json(['active' => auth()->user()->is_active]);
}
```

### ✅ 3. Update resources/views/student/waiting.blade.php
- Add polling JS: Every 5s fetch /student/check-active.
- If active → window.location = '{{ route(\"student.dashboard\") }}';
- Add refresh button + instructions.
- Keep existing content.

### ⏳ 4. Test
- php artisan route:clear
- Inactive student → /student/waiting
- Admin activates
- Refresh → auto-redirect in <10s

### ⏳ 5. Update TODOs
- Mark TODO-ACCOUNT-ACTIVATION.md complete
- Close this file ✅

**Current progress: Code implemented. Test the flow!**

