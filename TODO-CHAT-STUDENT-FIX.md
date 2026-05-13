# Chat Fix: Student Not Receiving Admin Messages

## Status: In Progress

### Step 1: Verify/Seed Administration Subject ✅
- Check SubjectSeeder.php
- Add Subject::create(['name' => 'Administration']) if missing
- Run php artisan db:seed --class=SubjectSeeder

### Step 2: Add JSON Endpoint for Polling ✅
- ChatController.php: getMessages(Subject $subject) → JSON messages
- Route in web.php
- ChatController.php: getMessages(Subject $subject) → JSON messages
- Route in web.php

### Step 3: Add Polling JS to student/chat.blade.php ✅
### Step 4: Add Polling JS to admin/chat.blade.php ✅

### Step 5: Test end-to-end ✅
