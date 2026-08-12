<?php

namespace App\Services;

use App\Models\ContactLead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ContactSpreadsheetSyncService
{
    public function sync(ContactLead $lead)
    {
        if (
            !config(
                'contact.sheet.enabled',
                false
            )
        ) {
            return false;
        }

        $url = trim(
            (string) config(
                'contact.sheet.webhook_url'
            )
        );

        if ($url === '') {
            Log::warning(
                'Synchronisation contacts désactivée : '
                . 'CONTACT_SHEET_WEBHOOK_URL est vide.'
            );

            return false;
        }

        try {
            $response = Http::asJson()
                ->timeout(
                    (int) config(
                        'contact.sheet.timeout',
                        8
                    )
                )
                ->post(
                    $url,
                    [
                        'secret' =>
                            config(
                                'contact.sheet.secret'
                            ),
                        'contact_id' =>
                            $lead->id,
                        'last_name' =>
                            $lead->last_name,
                        'first_name' =>
                            $lead->first_name,
                        'email' =>
                            $lead->email,
                        'phone' =>
                            $lead->phone,
                        'country' =>
                            $lead->country,
                        'reason' =>
                            $lead->latest_reason,
                        'submissions_count' =>
                            $lead->submissions_count,
                        'first_contact_at' =>
                            optional(
                                $lead->first_contact_at
                            )->toIso8601String(),
                        'last_contact_at' =>
                            optional(
                                $lead->last_contact_at
                            )->toIso8601String(),
                        'marketing_consent' =>
                            (bool)
                            $lead->marketing_consent,
                    ]
                );

            if (!$response->successful()) {
                Log::warning(
                    'Le tableau en ligne a refusé '
                    . 'la synchronisation du contact.',
                    [
                        'contact_id' =>
                            $lead->id,
                        'status' =>
                            $response->status(),
                        'body' =>
                            $response->body(),
                    ]
                );

                return false;
            }

            $lead->forceFill([
                'sheet_synced_at' => now(),
            ])->save();

            return true;
        } catch (Throwable $exception) {
            Log::warning(
                'Échec de synchronisation du contact '
                . 'vers le tableau en ligne.',
                [
                    'contact_id' =>
                        $lead->id,
                    'exception' =>
                        $exception->getMessage(),
                ]
            );

            return false;
        }
    }
}
