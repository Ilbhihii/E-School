# TODO-FIX-TESTS.md

## Current Status
- ✅ OpenAI Laravel package installed (`composer require openai-php/laravel:"^0.2"`)
- ✅ Service provider publish attempted (no config published - possibly auto-configured)
- ✅ Composer autoload dumped
- ✅ Config cached

## Remaining Steps
1. **Add your OpenAI API key to `.env`**:
   ```
   OPENAI_API_KEY=sk-proj-... (get from https://platform.openai.com/api-keys)
   ```

2. **Clear config cache** (if needed):
   ```
   php artisan config:clear
   ```

3. **Restart server**:
   ```
   php artisan serve
   ```

4. **Test the fix**:
   - Login as prof
   - Go to `/prof/tests/create`
   - Upload a PDF/DOCX file to trigger QCM generation
   - Or visit `/profAction` if that's the direct route

## Expected Result
The "Class OpenAI\Laravel\Facades\OpenAI not found" error should be resolved. QCM generation will work if API key is valid.

---

**Next**: Generate and run unit/feature tests for TestController and QCMGenerator after you complete the above steps.
