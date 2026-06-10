# Fix 404 for /prof/courses/create

## Status: Backend Fixed ✅

**Backend verified:**
- Routes: `prof.courses.*` defined via resource controller
- Controller: ProfController has create/store methods
- Views: All use correct `route()` helpers

**Root cause:** Client-side URL malformation (`"%22http://127.0.0.1:8000/prof/courses/create/%22`)

## Remaining Steps:
1. Restart server: `php artisan serve`
2. Hard refresh (Ctrl+Shift+R) & clear browser cache
3. DevTools → Network tab → reproduce → note initiator file/line
4. Check Console tab for JS errors
5. Verify login as **prof role** user

**Test sequence:**
```
1. /prof/courses (should load index)
2. Click \"Créer\" → /prof/courses/create (should load form)
```

**If still 404:**
- Share Network tab screenshot of failing request
- Run `php artisan route:list | findstr prof.courses` output
- Browser/version used

Caches cleared. Ready to test.
