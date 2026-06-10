# CLASS_ROOMS TABLE FIX
Status: In progress

## Step 1: ✅ Model and class_rooms migration fixed

## Step 2: ✅ Fix controllers (StudentController.php duplicate imports removed)

## Step 3: [ ] Fix subjects rename migration and complete migrate:fresh
- Edit rename migration to be idempotent
- php artisan migrate:rollback --step=20 
- php artisan migrate

## Step 4: [ ] Clear caches
php artisan config:clear &amp;&amp; php artisan cache:clear

## Step 5: [ ] Test /prof/devoir
