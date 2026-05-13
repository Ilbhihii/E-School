# Fix /student/checkout MethodNotAllowed Error

## Steps:
- [x] 1. Add GET route for /student/checkout in routes/web.php (PaymentController::index) ✅
- [x] 2. Clear and cache routes (php artisan route:clear && php artisan route:cache) ✅
- [x] 3. Verify routes with php artisan route:list | findstr checkout ✅ (GET student.checkout.form and POST student.checkout both present)
- [x] 4. Test GET http://127.0.0.1:8000/student/checkout → should show payment view ✅
- [x] 5. Test POST from /student/plans forms → Stripe redirect (unchanged) ✅
- [x] 6. Mark complete and attempt_completion ✅

## COMPLETE
Fixed MethodNotAllowed error by adding GET /student/checkout route (PaymentController::index → payment.blade.php view).
Direct access now works. Forms POST unchanged.
