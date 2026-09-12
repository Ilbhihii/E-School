<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeworkSmsService
{
    public function send(?string $phone, string $message): bool
    {
        if (!config('homework_reminders.sms.enabled')) {
            return false;
        }

        $phone = trim((string) $phone);

        if ($phone === '') {
            return false;
        }

        if (config('homework_reminders.sms.provider') !== 'twilio') {
            Log::warning('Homework SMS provider not supported.');
            return false;
        }

        $sid = config('homework_reminders.sms.twilio_sid');
        $token = config('homework_reminders.sms.twilio_token');
        $from = config('homework_reminders.sms.twilio_from');

        if (!$sid || !$token || !$from) {
            Log::warning('Homework SMS enabled but Twilio is not configured.');
            return false;
        }

        $response = Http::asForm()
            ->withBasicAuth($sid, $token)
            ->post(
                'https://api.twilio.com/2010-04-01/Accounts/'
                    . $sid
                    . '/Messages.json',
                [
                    'From' => $from,
                    'To' => $phone,
                    'Body' => $message,
                ]
            );

        if (!$response->successful()) {
            Log::warning(
                'Homework SMS failed: ' . $response->body()
            );
            return false;
        }

        return true;
    }
}
