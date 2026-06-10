# TODO: QCM System Fix Progress

## [x] 1. Install OpenAI package ✅
```bash
composer require openai-php/laravel
php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider"
php artisan config:cache
```
*Note: Add OPENAI_API_KEY=sk-... to .env*

## [x] 2. Migration created and edited ✅
```bash
php artisan make:migration rename_course_id_to_subject_id_in_tests_table --table=tests
```
*Migration ready for php artisan migrate*

## [x] 3. Create app/Services/QCMGenerator.php service ✅

## [ ] 4. Update app/Http/Controllers/TestController.php
- Implement parseQCM using service
- Remove convertToQCM
- Fix submit() for checkboxes

## [x] 5. Update resources/views/student/tests/show.blade.php (checkboxes) ✅

## [x] 6. Fix resources/views/prof/tests/create.blade.php (add container) ✅

## [x] 7. Run migration ✅
```bash
php artisan migrate
```

## [ ] 8. Test
- Create test from PDF (add OPENAI key)
- Verify AI multi-QCM
- Submit checkboxes, check score/logic

## [x] 9. Clear caches ✅
```bash
php artisan route:clear view:clear config:clear
```
