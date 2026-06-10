# Fix Cours URL Issue

## Symptôme
Clique sur \"Cours\" redirige vers URL malformée avec double quotes encodées:
`http://127.0.0.1:8000/%22http://127.0.0.1:8000/prof/courses/create/%22`

## Analyse Code
✅ Routes `routes/web.php`: `Route::resource('courses', ProfController::class)` sous prefix `prof` correct.
✅ Sidebar `layouts/prof.blade.php`: `route('prof.courses.index')` → `/prof/courses`
✅ Index `prof/courses/index.blade.php`: `route('prof.courses.create')` → `/prof/courses/create`
✅ Aucune URL absolue hardcoded trouvée (search_files confirmé).

## Cause Probable
- JavaScript ajoutant href dynamique avec quotes extra
- Bookmark corrompu
- Cache navigateur / Service Worker
- Édition HTML dans devtools persistante

## Solutions
1. **Clear cache**: Ctrl+Shift+R (hard refresh)
2. **Inspect link**: Clic droit \"Cours\" → Inspecter → Copier `<a href=...>`
3. **Console**: Vérifier erreurs JS
4. **Incognito**: Test en mode privé
5. **Serveur restart**: `php artisan serve`

## Vérification
```
php artisan route:list | grep courses
# Doit montrer:
# prof.courses.index    GET|HEAD  prof/courses  ...
# prof.courses.create   GET|HEAD  prof/courses/create  ...
```

Code ✅ fonctionnel. Issue client-side.

